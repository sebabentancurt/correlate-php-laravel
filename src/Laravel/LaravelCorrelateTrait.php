<?php

namespace Amp\Correlate\Laravel;

use Closure;
use Monolog\Logger;
use Amp\Correlate\CorrelateProcessor;
use Amp\Correlate\Correlate;
use Illuminate\Http\Request;

trait LaravelCorrelateTrait
{
    /** @var LoggerInterface */
    protected $log;

    /** @var Request */
    private $request;

    private $tracking_id;

    /**
     * Funciona como constructor del Trait
     * 
     * @param Request $request
     * @param string $tracking_id
     * 
     * @return void
     */
    public function correlateId(Request $request, string $tracking_id = null)
    {
        $this->log = \Log::getMonolog();
        $this->request = $request;
        $this->tracking_id = $tracking_id;

        $this->installMacrosCorrelateID();
        $this->setCorrelateID();
    }

    /**
     * @return mixed
     */
    private function setCorrelateID()
    {
        if (!$this->request->headers->has(Correlate::getHeaderName())) {
            $tracking_id =  $this->tracking_id ? (string) Correlate::id() : $this->tracking_id;
            $this->request->headers->set(Correlate::getHeaderName(), $tracking_id);
        }

        $processor = new CorrelateProcessor(
            Correlate::getParamName(),
            $this->request->headers->get(Correlate::getHeaderName())
        );

        if ($this->log instanceof Logger) {
            $this->log->pushProcessor($processor);
        } elseif (method_exists($this->log, 'getMonolog')) {
            $this->log->getMonolog()->pushProcessor($processor);
        }
    }

    /**
     * Install macros for request and response classes.
     */
    private function installMacrosCorrelateID()
    {
        if (!$this->request::hasMacro('hasCorrelationId')) {
            $this->request::macro('hasCorrelationId', function() {
                if ($this->headers->has(Correlate::getHeaderName())) {
                    return true;
                }
                return false;
            });
        }

        if (!$this->request::hasMacro('getCorrelationId')) {
            $this->request::macro('getCorrelationId', function($default = null) {
                if ($this->headers->has(Correlate::getHeaderName())) {
                    return $this->headers->get(Correlate::getHeaderName());
                }
                return $default;
            });
        }

        if (!$this->request::hasMacro('setCorrelationId')) {
            $this->request::macro('setCorrelationId', function($cid)  {
                $this->headers->set(Correlate::getHeaderName(), (string) $cid);
                return $this;
            });
        }
    }
}
