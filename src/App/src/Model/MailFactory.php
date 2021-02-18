<?php
/**
* Podips Monitor
* @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
* @copyright 2021 FGSL
*
**/
declare(strict_types=1);

namespace App\Handler;

use App\Model\Mail;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MailFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        $mail = $container->get('config')['mail'];

        return new Mail($mail);
    }
}
