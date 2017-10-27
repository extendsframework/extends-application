<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\BootstrapProviderInterface;
use ExtendsFramework\Console\Terminal\TerminalInterface;
use ExtendsFramework\Http\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * HTTP Server.
     *
     * Test that application will run as HTTP server.
     *
     * @covers \ExtendsFramework\Application\Application::__construct()
     * @covers \ExtendsFramework\Application\Application::run()
     * @covers \ExtendsFramework\Application\Application::getServer()
     */
    public function testHttpServer(): void
    {
        Buffer::setSapi('cgi');

        $server = $this->createMock(ServerInterface::class);
        $server
            ->expects($this->once())
            ->method('run');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(ServerInterface::class)
            ->willReturn($server);

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $module = new ModuleBootstrapStub();
        $application = new Application($serviceLocator, [
            $module
        ]);
        $application->run();

        $this->assertTrue($module->isCalled());

        Buffer::reset();
    }

    /**
     * Console terminal.
     *
     * Test that application will run as console terminal.
     *
     * @covers \ExtendsFramework\Application\Application::__construct()
     * @covers \ExtendsFramework\Application\Application::run()
     * @covers \ExtendsFramework\Application\Application::getTerminal()
     */
    public function testConsoleTerminal(): void
    {
        Buffer::setSapi('cli');

        $terminal = $this->createMock(TerminalInterface::class);
        $terminal
            ->expects($this->once())
            ->method('run');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(TerminalInterface::class)
            ->willReturn($terminal);

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $module = new ModuleBootstrapStub();
        $application = new Application($serviceLocator, [
            $module
        ]);
        $application->run();

        $this->assertTrue($module->isCalled());

        Buffer::reset();
    }
}

class ModuleBootstrapStub implements ModuleInterface, BootstrapProviderInterface
{
    /**
     * @var bool
     */
    protected $called = false;

    /**
     * @inheritDoc
     */
    public function onBootstrap(ServiceLocatorInterface $serviceLocator): void
    {
        $this->called = true;
    }

    /**
     * @return bool
     */
    public function isCalled(): bool
    {
        return $this->called;
    }
}

class Buffer
{
    protected static $sapi;

    public static function getSapi(): string
    {
        return static::$sapi;
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
