<?php

namespace Amp\Correlate\Laravel;

use Closure;
use Illuminate\Http\Request;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Amp\Correlate\CorrelateProcessor;
use Amp\Correlate\Correlate;
use Illuminate\Log\LogManager;

class LaravelCorrelateMiddleware
{
    /** @var LoggerInterface */
    protected $log;

    public function __construct(LoggerInterface $log = null)
    {
        $this->log = $log;

        $this->installMacros();
    }

    /**
     * Install macros for request and response classes.
     */
    protected function installMacros()
    {
        if (!Request::hasMacro('hasCorrelationId')) {
            Request::macro('hasCorrelationId', function() {
                if ($this->headers->has(Correlate::getHeaderName())) {
                    return true;
                }
                return false;
            });
        }

        if (!Request::hasMacro('getCorrelationId')) {
            Request::macro('getCorrelationId', function($default = null) {
                if ($this->headers->has(Correlate::getHeaderName())) {
                    return $this->headers->get(Correlate::getHeaderName());
                }
                return $default;
            });
        }

        if (!Request::hasMacro('setCorrelationId')) {
            Request::macro('setCorrelationId', function($cid)  {
                $this->headers->set(Correlate::getHeaderName(), (string) $cid);
                return $this;
            });
        }
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has(Correlate::getHeaderName())) {
            $request->headers->set(Correlate::getHeaderName(), (string)Correlate::id());
        }

        $processor = new CorrelateProcessor(
            Correlate::getParamName(),
            $request->headers->get(Correlate::getHeaderName())
        );

        if ($this->log instanceof Logger) { //Compatibility Laravel 5.x
            $this->log->pushProcessor($processor);
        } elseif($this->log instanceof LogManager){ //Compatibility Laravel 6.x
            $this->log->getLogger()->pushProcessor($processor);
        } elseif (method_exists($this->log, 'getMonolog')) {
            $this->log->getMonolog()->pushProcessor($processor);
        }

        return $next($request);
    }
}
