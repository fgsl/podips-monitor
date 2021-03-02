<?php
/**
 * Podips Monitor
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 * @copyright 2021 FGSL
 */
namespace App\Model;

use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;

class Mail
{
    /** @var TransportInterface **/
    private $transport;
    /** @var Message **/
    private $message;
    
    public function __construct(array $config)
    {
        $this->transport = new SmtpTransport();
        try {
            $options = new SmtpOptions([
                'name' => $config['name'],
                'host' => $config['host'],
                'connection_class' => 'login',
                'connection_config' => [
                    'username' => $config['connection_config']['username'],
                    'password' => $config['connection_config']['password']
                ]
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $options = [];
        }
        $recipientmail = getenv('PODIPS_RECIPIENT_MAIL');
        if (empty($recipientmail)) {
            $recipientmail = $config['recipientmail'];
        }
        $this->transport->setOptions($options);
        $this->message = new Message();
        $this->message->setEncoding('UTF-8');
        $this->message->addFrom($config['sendermail'], $config['sendername']);
        $this->message->addTo($recipientmail);
    }
    
    /**
     * @param string $subject
     * @param string $body
     */
    public function sendMessage(string $subject,string $body)
    {
        $this->message->setSubject($subject);
        $this->message->setBody($body);        
        $this->transport->send($this->message);
    }
}
