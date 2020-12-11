<?php
/**
* Podips Monitor
* @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
* @copyright 2020 FGSL
*
**/
declare(strict_types=1);

namespace App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        $template = $container->get(TemplateRendererInterface::class);

        return new HomePageHandler($template);
    }
}
