<?php
/**
 * Podips Monitor
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 * @copyright 2020 FGSL
 * 
 * @OA\Get(
 *     path="/podips/{operation}",
 *     @OA\Parameter(
 *         name="operation",
 *         in="path",
 *         description="operação (write,read,all)",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="200", description="verifica se o podips está gravando no Fluentd. Retorna esperado: success")
 * )
 */
declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Model\Monitor;

class PodipsHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        
        $operation = $request->getAttribute('operation');
        
        $status = 'success';
        try {
            $jsonWriter = Monitor::getInstance()->getPodipsWriterStatus();
            $jsonReader = Monitor::getInstance()->getPodipsReaderStatus();
            
            $currentDateTime = new \DateTime();
            $writerDateTime = new \DateTime($jsonWriter->datetime);
            $readerDateTime = new \DateTime($jsonReader->datetime);
            
            $diffWriter = $currentDateTime->diff($writerDateTime);
            $diffReader = $currentDateTime->diff($readerDateTime);

            $operations = ['write','read', 'all'];
            
            if (in_array($operation, $operations)) {
                if ($operation == 'write' && $jsonWriter->code != 200){
                    $status = $jsonWriter->message;
                }
                if (($operation == 'write' || $operation == 'all') && $this->elapsedSeconds($diffWriter) > 300){
                    $status = 'no logs for more than 5 minutes';
                }
                if ($operation == 'read' && $jsonReader->code != 200){
                    $status = $jsonReader->message;
                }
                if (($operation == 'read' || $operation == 'all')  && $this->elapsedSeconds($diffReader) > 300){
                    $status = 'no logs for more than 5 minutes';
                }
                if ($operation == 'all' && !($jsonReader->code == 200 && $jsonWriter->code == 200) ) {
                    $status = 'fail';
                }
            } else {
                $status = 'invalid operation';
            }            
        } catch (\Exception $e) {
            $status = $e->getMessage();
        } 

        return new JsonResponse(['status' => $status]);
    }
    
    /**
     * @return integer elapsed time in seconds
     */
    private function elapsedSeconds(\DateInterval $interval)
    {
        $elapsedSeconds = ($interval->s);
        $elapsedSeconds+= ($interval->i * 60);
        $elapsedSeconds+= ($interval->h * 60 * 60);
        $elapsedSeconds+= ($interval->d * 24 * 60 * 60);
        $elapsedSeconds+= ($interval->m * 30 * 24 * 60 * 60);
        $elapsedSeconds+= ($interval->y * 365 * 30 * 24 * 60 * 60);
        return $elapsedSeconds;
    }
}