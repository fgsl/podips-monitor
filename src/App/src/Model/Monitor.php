<?php
/**
 * Podips Monitor
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 * @copyright 2020 FGSL
 */
namespace App\Model;

class Monitor
{
    const PODIPS_WRITER_STATUS = 'podips-writer-status.json';
    const PODIPS_READER_STATUS = 'podips-reader-status.json';
    const LAST_KUBERNETES_READING = 'last-kubernetes-reading.json';
    const LAST_QUEUE_WRITING = 'last-queue-writing.json';
    const LAST_QUEUE_READING = 'last-queue-reading.json';
    const LAST_LOG_WRITING = 'last-log-writing.json';
    const DATA_FOLDER = APP_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
    
    /** @var Monitor **/
    private static $instance;
        
    private function __construct()
    {
        
    }
    
    public static function getInstance()
    {
        if (self::$instance == null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPodipsReaderStatus()
    {
        $path = self::DATA_FOLDER . self::PODIPS_READER_STATUS;
        $kubernetesStatus = $this->getKubernetesReadingStatus();
        $queueWritingStatus = $this->getQueueWritingStatus();
        $code = ($kubernetesStatus->code == '200' && $queueWritingStatus->code == '200' ? '200' : '500');
        $message = ($code == '200' ? 'success' : 'fail');
        $datetime = $queueWritingStatus->datetime;
        $json = <<<JSON
        {
            "code" : "$code",
            "message" : "$message",
            "datetime" : "$datetime"
        }
JSON;
        $this->setPodipsReaderStatus($json);
        return $this->getJsonFile($path);
    }

    public function getPodipsWriterStatus()
    {
        $path = self::DATA_FOLDER . self::PODIPS_WRITER_STATUS;
        $logStatus = $this->getFluentdWritingStatus();
        $queueReadingStatus = $this->getQueueReadingStatus();
        $code = ($logStatus->code == '200' && $queueReadingStatus->code == '200' ? '200' : '500');
        $message = ($code == '200' ? 'success' : 'fail');
        $datetime = $logStatus->datetime;
        $json = <<<JSON
        {
            "code" : "$code",
            "message" : "$message",
            "datetime" : "$datetime"
        }
JSON;
        $this->setPodipsWriterStatus($json);        
        return $this->getJsonFile($path);
    }
    
    public function getKubernetesReadingStatus()
    {
        $path = self::DATA_FOLDER . self::LAST_KUBERNETES_READING;
        return $this->getJsonFile($path);
    }
    
    public function getQueueWritingStatus()
    {   
        $path = self::DATA_FOLDER . self::LAST_QUEUE_WRITING;
        return $this->getJsonFile($path);
    }
    
    public function getQueueReadingStatus()
    {
        $path = self::DATA_FOLDER . self::LAST_QUEUE_READING;
        return $this->getJsonFile($path);
    }
    
    public function getFluentdWritingStatus()
    {
        $path = self::DATA_FOLDER . self::LAST_LOG_WRITING;
        return $this->getJsonFile($path);
    }

    public function setKubernetesReadingStatus($json)
    {
        $path = self::DATA_FOLDER . self::LAST_KUBERNETES_READING;
        file_put_contents($path, $json);
    }
    
    public function setQueueWritingStatus($json)
    {
        $path = self::DATA_FOLDER . self::LAST_QUEUE_WRITING;
        file_put_contents($path, $json);
    }
    
    public function setQueueReadingStatus($json)
    {
        $path = self::DATA_FOLDER . self::LAST_QUEUE_READING;
        file_put_contents($path, $json);
    }
    
    public function setFluentdWritingStatus($json)
    {
        $path = self::DATA_FOLDER . self::LAST_LOG_WRITING;
        file_put_contents($path,$json);
    }

    public function setPodipsReaderStatus($json)
    {
        $path = self::DATA_FOLDER . self::PODIPS_READER_STATUS;
        file_put_contents($path,$json);
    }

    public function setPodipsWriterStatus($json)
    {
        $path = self::DATA_FOLDER . self::PODIPS_WRITER_STATUS;
        file_put_contents($path,$json);
    }    
    
    private function getJsonFile($path)
    {
        $datetime = date(\Datetime::ISO8601);
        try {
            if (!file_exists($path)){
                $json = <<<JSON
{
    "code" : "0",
    "message" : "no message",
    "datetime": "$datetime",
    "remoteaddr" : "unknown",
    "remotehost" : "unknown"
}
JSON;
                file_put_contents($path, $json);
            }
            $json = json_decode(file_get_contents($path));            
        } catch (\Exception $e) {
            $jsonError = '{"code": "0","message": "error","datetime" : "error", "remoteaddr": "error", "remotehost": error"}';
            $json = json_decode($jsonError);            
        }
        return $json;
   }    
}