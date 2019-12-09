<?php

namespace Amp\Correlate\Laravel;

use Closure;
use Monolog\Logger;
use Amp\Correlate\CorrelateProcessor;
use Amp\Correlate\Correlate;

trait LaravelCorrelateTrait
{
    /** @var LoggerInterface */
    protected $log;

    public function correlateId()
    {
        $this->log = \Log::getMonolog();
        $this->setCorrelateID();
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function setCorrelateID()
    {
        $correlate = (string) Correlate::id();

        $processor = new CorrelateProcessor(
            Correlate::getParamName(),
            $correlate
        );

        if ($this->log instanceof Logger) {
            $this->log->pushProcessor($processor);
        } elseif (method_exists($this->log, 'getMonolog')) {
            $this->log->getMonolog()->pushProcessor($processor);
        }

    }
}
