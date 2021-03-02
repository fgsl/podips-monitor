<?php
/**
 * @OA\Info(title="Podips Monitor", version="1.1.0")
 * @copyright 2020 FGSL
 */
declare(strict_types=1);

namespace App\Handler;

use App\Model\Mail;
use App\Model\Monitor;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandler implements RequestHandlerInterface
{
    /** @var Mail */
    private $mail;

    /** @var null|TemplateRendererInterface */
    private $template;

    public function __construct(TemplateRendererInterface $template, Mail $mail) {
        $this->template = $template;
        $this->mail     = $mail;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $podipsHost = $_SERVER['REQUEST_URI'];
    
        $json = Monitor::getInstance()->getKubernetesReadingStatus();
        $color = $this->getColor($json);
        $kubernetesReadingStatusBlock = $this->getBlock($json, $color);
        $kubernetesReadingSendMailStatus = $this->sendMessage((int)$json->code,'Fail to read Kubernetes', $kubernetesReadingStatusBlock . "<p>$podipsHost</p>");      
        $kubernetesReadingStatusBlock .= "<p>$kubernetesReadingSendMailStatus</p>";

        $json = Monitor::getInstance()->getQueueWritingStatus();
        $color = $this->getColor($json);
        $queueWritingStatusBlock = $this->getBlock($json, $color);
        $queueWritingSendMailStatus = $this->sendMessage((int)$json->code, 'Fail to write Queue', $queueWritingStatusBlock . "<p>$podipsHost</p>");
        $queueWritingStatusBlock .= "<p>$queueWritingSendMailStatus</p><p>$podipsHost</p>";
        
        $json = Monitor::getInstance()->getQueueReadingStatus();    
        $color = $this->getColor($json);
        $queueReadingStatusBlock = $this->getBlock($json, $color);   
        $queueReadingSendMailStatus = $this->sendMessage((int)$json->code, 'Fail to read Queue', $queueReadingStatusBlock . "<p>$podipsHost</p>");
        $queueReadingStatusBlock .= "<p>$queueReadingSendMailStatus</p>";
        
        $json = Monitor::getInstance()->getFluentdWritingStatus();
        $color = $this->getColor($json);
        $fluentdWritingStatusBlock = $this->getBlock($json, $color);
        $fluentdWritingSendMailStatus = $this->sendMessage((int)$json->code, 'Fail to write Fluentd', $fluentdWritingStatusBlock . "<p>$podipsHost</p>");
        $fluentdWritingStatusBlock .= "<p>$fluentdWritingSendMailStatus</p>";
        
        $data = [
            'kubernetesReadingStatusBlock'      => $kubernetesReadingStatusBlock,
            'queueWritingStatusBlock'           => $queueWritingStatusBlock,
            'queueReadingStatusBlock'           => $queueReadingStatusBlock,
            'fluentdWritingStatusBlock'         => $fluentdWritingStatusBlock
        ];

        return new HtmlResponse($this->template->render('app::home-page', $data));
    }
    
    /**
     * @param Object $json
     * @param string $color
     * @return string
     */
    private function getBlock(Object $json, string $color):string
    {
        $block = <<<BLOCK
        <p style="color: $color">
		code: {$json->code}<br/>
		message: {$json->message}<br/>
		datetime: {$json->datetime}<br/>
		remote address: {$json->remoteaddr}<br/>
		remote host: {$json->remotehost}
        </p>
BLOCK;   
        return $block;
    }
    
    private function sendMessage(int $code, string $subject, string $block): string
    {        
        $sendMail = (bool) getenv('PODIPS_SEND_MAIL');
        $result = 'E-mail sending is disabled';
        if ($code != 200 && $sendMail){
            try {
                $this->mail->sendMessage($subject, $block);
                $result = 'Notification sent by e-mail';
            } catch (\Exception $e) {
                error_log($e->getMessage());
                $result = $e->getMessage();
            }
        }
        return $result;
    }
    
    /**
     * @param string $jsonDateTime
     * @return int
     */
    private function getElapsedTime(string $jsonDateTime): int
    {
        $d1 = new \DateTime($jsonDateTime);
        $d2 = new \DateTime('NOW');
        $interval = $d2->diff($d1);
        $minutes = ($interval->s / 60);
        $minutes += $interval->i;
        $minutes += ($interval->h * 60);   
        
        return (int) $minutes;
    }
    
    private function getColor(Object $json): string
    {
        $color = ($json->code == 200 ? 'green' : 'red');        
        $elapsedTime = $this->getElapsedTime($json->datetime);
        if ($elapsedTime > 5 && $color == 'green') {            
            $color = 'orange';
        }        
        return $color;
    }
}
