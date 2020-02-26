<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http\Renderer;

class Buffer
{
    /**
     * @var array
     */
    protected static $headers = [];

    /**
     * @var int
     */
    protected static $code;

    /**
     * @return array
     */
    public static function getHeaders(): array
    {
        return self::$headers;
    }

    /**
     * @return int
     */
    public static function getCode(): int
    {
        return self::$code;
    }

    /**
     * @param string $header
     */
    public static function addHeader(string $header): void
    {
        self::$headers[] = $header;
    }

    /**
     * @param $code
     */
    public static function setCode($code): void
    {
        self::$code = $code;
    }

    public static function reset(): void
    {
        static::$headers = [];
        static::$code = null;
    }
}
