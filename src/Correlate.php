<?php

/**
 * @author Soma Szelpal <szelpalsoma@gmail.com>
 * @license MIT
 */
namespace Amp\Correlate;

use Closure;

/**
 * Class Correlate
 *
 * @package ProEmergotech\Correlate
 */
class Correlate
{
    /**
     * @var string
     */
    const DEFAULT_HEADER_NAME = 'X-Correlation-ID';

    /**
     * @var string
     */
    const DEFAULT_PARAM_NAME = 'x_correlation_id';

    /**
     * @var string
     */
    protected static $headerName = self::DEFAULT_HEADER_NAME;

    /**
     * @var string
     */
    protected static $paramName = self::DEFAULT_PARAM_NAME;

    /**
     * @var Closure
     */
    protected static $generatorFunc = null;

    /**
     * Creates a correlation id
     * @return string
     */
    public static function id(): string
    {
        return (string) self::getGeneratorFunc()();
    }

    /**
     * @return string
     */
    public static function getHeaderName(): string
    {
        return self::$headerName;
    }

    /**
     * @param string $val
     */
    public static function setHeaderName(string $val)
    {
        self::$headerName = $val;
    }

    /**
     * @return string
     */
    public static function getParamName(): string
    {
        return self::$paramName;
    }

    /**
     * @param string $val
     */
    public static function setParamName(string $val)
    {
        self::$paramName = $val;
    }

    /**
     * @return [type] [description]
     */
    public static function getGeneratorFunc(): Closure
    {
        if (self::$generatorFunc instanceof Closure) {
            return self::$generatorFunc;
        }

        return function () {
            return (string) \Webpatser\Uuid\Uuid::generate(4);
        };
    }

    /**
     * @param Closure $g Function that returns a random identifier
     */
    public function setGeneratorFunc(Closure $g)
    {
        self::$generatorFunc = $g;
    }
}
