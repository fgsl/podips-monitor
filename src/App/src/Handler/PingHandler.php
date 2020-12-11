<?php
/**
* Podips Monitor
* @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
* @copyright 2020 FGSL
**/

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function time;

class PingHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(['ack' => time()]);
    }
}
