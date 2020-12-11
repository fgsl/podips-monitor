<?php
/**
 * Podips Monitor
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 * @copyright 2020 FGSL
 * 
 * @OA\Get(
 *     path="/queue/{operation}/{code}/{message}",
 *     @OA\Parameter(
 *         name="operation",
 *         in="path",
 *         description="operação (write,read)",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="code",
 *         in="path",
 *         description="código de status HTTP",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="message",
 *         in="path",
 *         description="mensagem de status",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="verifica se o podips está gravando no Fluentd")
 * )
 */

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Model\Monitor;

class QueueHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        
        $operation = $request->getAttribute('operation');
        $code = $request->getAttribute('code');
        $message = $request->getAttribute('message');
        $datetime = date(\DateTimeInterface::ISO8601);
        $remoteaddr = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown');
        $remotehost = (isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : 'unknown');        
        $json = <<<JSON
{
    "code" : "$code",
    "message" : "$message",
    "datetime" : "$datetime",
    "remoteaddr" : "$remoteaddr",
    "remotehost" : "$remotehost" 
}
JSON;        
        
        $status = 'success';
        try {
            if ($operation == 'write'){
                Monitor::getInstance()->setQueueWritingStatus($json);
            } elseif($operation == 'read'){
                Monitor::getInstance()->setQueueReadingStatus($json);
            } else {
                $status = 'invalid operation';
            }            
        } catch (\Exception $e) {
            $status = $e->getMessage();
        } 

        return new JsonResponse(['status' => $status]);
    }
}
