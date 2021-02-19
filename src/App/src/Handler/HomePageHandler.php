<?php
/**
 * @OA\Info(title="Podips Monitor", version="1.0.0")
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
        $json = Monitor::getInstance()->getKubernetesReadingStatus();
        $color = ($json->code == 200 ? 'green' : 'red');
        $kubernetesReadingStatusBlock = $this->getBlock($json, $color);
        $this->sendMessage($json->code,'Fail to read Kubernetes', $kubernetesReadingStatusBlock);
        
        $json = Monitor::getInstance()->getQueueWritingStatus();
        $color = ($json->code == 200 ? 'green' : 'red');
        $queueWritingStatusBlock = $this->getBlock($json, $color);
        $this->sendMessage($json->code, 'Fail to write Queue', $queueWritingStatusBlock);
        
        $json = Monitor::getInstance()->getQueueReadingStatus();    
        $color = ($json->code == 200 ? 'green' : 'red');
        $queueReadingStatusBlock = $this->getBlock($json, $color);   
        $this->sendMessage($json->code, 'Fail to read Queue', $queueReadingStatusBlock);
        
        $json = Monitor::getInstance()->getFluentdWritingStatus();
        $color = ($json->code == 200 ? 'green' : 'red');
        $fluentdWritingStatusBlock = $this->getBlock($json, $color);
        $this->sendMessage($json->code, 'Fail to write Fluentd', $fluentdWritingStatusBlock);
        
        $data = [
            'kubernetesReadingStatusBlock'  => $kubernetesReadingStatusBlock,
            'queueWritingStatusBlock'       => $queueWritingStatusBlock,
            'queueReadingStatusBlock'       => $queueReadingStatusBlock,
            'fluentdWritingStatusBlock'     => $fluentdWritingStatusBlock
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
    
    private function sendMessage(int $code, string $subject, string $block)
    {
        if ($code != 200){
            try {
                $this->mail->sendMessage($subject, $block);
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }
    }
}