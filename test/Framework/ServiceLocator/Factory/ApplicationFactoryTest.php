<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Factory;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Console\Terminal\TerminalInterface;
use ExtendsFramework\Http\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class ApplicationFactoryTest extends TestCase
{
    /**
     * Create HTTP application.
     *
     * Test that factory will create an instance of ApplicationInterface.
     *
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::createService()
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::getHttpApplication()
     */
    public function testCreateHttpApplication(): void
    {
        Buffer::setSapi('cgi');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(ServerInterface::class)
            ->willReturn($this->createMock(ServerInterface::class));

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $factory = new ApplicationFactory();
        $application = $factory->createService(ApplicationInterface::class, $serviceLocator, [
            'modules' => [],
        ]);

        $this->assertInstanceOf(ApplicationInterface::class, $application);

        Buffer::reset();
    }

    /**
     * Create console application.
     *
     * Test that factory will create an instance of ApplicationInterface.
     *
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::createService()
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::getConsoleApplication()
     */
    public function testCreateConsoleApplication(): void
    {
        Buffer::setSapi('cli');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(TerminalInterface::class)
            ->willReturn($this->createMock(TerminalInterface::class));

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $factory = new ApplicationFactory();
        $application = $factory->createService(ApplicationInterface::class, $serviceLocator, [
            'modules' => [],
        ]);

        $this->assertInstanceOf(ApplicationInterface::class, $application);

        Buffer::reset();
    }
}

class Buffer
{
    protected static $sapi;

    public static function getSapi(): string
    {
        return static::$sapi ?: 'cli';
    }

    public static function setSapi(string $sapi): void
    {
        static::$sapi = $sapi;
    }

    public static function reset(): void
    {
        static::$sapi = null;
    }
}

function php_sapi_name()
{
    return Buffer::getSapi();
}
