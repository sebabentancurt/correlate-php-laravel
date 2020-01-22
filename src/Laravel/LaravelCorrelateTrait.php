<?php

namespace Amp\Correlate\Laravel;

use Closure;
use Monolog\Logger;
use Amp\Correlate\CorrelateProcessor;
use Amp\Correlate\Correlate;
use Illuminate\Http\Request;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Log;

trait LaravelCorrelateTrait
{
    /** @var LoggerInterface */
    protected $log;

    /** @var Request */
    private $request;

    private $tracking_id;

    /**
     * Setea el tracking id a la request
     * 
     * @param Request $request
     * @param string $tracking_id
     * 
     * @return void
     */
    public function correlateId(Request $request, string $tracking_id = null)
    {
        $this->log = resolve('log');
        $this->request = $request;
        $this->tracking_id = $tracking_id;

        $this->setCorrelateIdToRequest();
        $this->setCorrelateIdToLogger();
        $this->installMacrosCorrelateID();
    }

    /**
     * @return mixed
     */
    private function setCorrelateIdToRequest()
    {
        $tracking_id =  $this->tracking_id ? $this->tracking_id : (string) Correlate::id();
        $this->request->headers->set(Correlate::getHeaderName(), $tracking_id);
    }

    private function setCorrelateIdToLogger(){
        $processor = new CorrelateProcessor(
            Correlate::getParamName(),
            $this->request->headers->get(Correlate::getHeaderName())
        );

        if ($this->log instanceof Logger) { //Compatibility Laravel 5.x
            $logger = $this->log;
        } elseif($this->log instanceof LogManager){ //Compatibility Laravel 6.x
            $logger = $this->log->getLogger();
        } elseif (method_exists($this->log, 'getMonolog')) {
            $logger = $this->log->getMonolog();
        }

        while(!empty($logger->getProcessors())){
            $logger->popProcessor();
        }

        $logger->pushProcessor($processor);
    }

    /**
     * Install macros for request and response classes.
     */
    private function installMacrosCorrelateID()
    {
        $this->request::macro('hasCorrelationId', function() {
            if ($this->headers->has(Correlate::getHeaderName())) {
                return true;
            }
            return false;
        });
    
        $this->request::macro('getCorrelationId', function($default = null) {
            if ($this->headers->has(Correlate::getHeaderName())) {
                return $this->headers->get(Correlate::getHeaderName());
            }
            return $default;
        });
    
        $this->request::macro('setCorrelationId', function($cid)  {
            $this->headers->set(Correlate::getHeaderName(), (string) $cid);
            return $this;
        });
    }
}
