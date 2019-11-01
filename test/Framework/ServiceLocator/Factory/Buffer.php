<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Factory;

class Buffer
{
    /**
     * @var string
     */
    protected static $sapi;

    /**
     * @return string
     */
    public static function getSapi(): string
    {
        return static::$sapi ?: 'cli';
    }

    /**
     * @param string $sapi
     */
    public static function setSapi(string $sapi): void
    {
        static::$sapi = $sapi;
    }

    public static function reset(): void
    {
        static::$sapi = null;
    }
}
