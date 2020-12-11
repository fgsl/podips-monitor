<?php
/**
 * Podips Monitor
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 * @copyright 2020 FGSL
 *
 * @OA\Get(
 *     path="/api",
 *     @OA\Response(response="200", description="API Swagger")
 * )
 */

declare(strict_types = 1);
namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class ApiHandler implements RequestHandlerInterface
{
    /** @var null|TemplateRendererInterface */
    private $template;
    
    public function __construct(TemplateRendererInterface $template = null)
    {
        $this->template = $template;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->template->render('api::index',['layout'=>false]));
    }
}
