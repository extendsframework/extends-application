<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ShutdownProviderInterface;
use ExtendsFramework\Application\Module\Provider\StartupProviderInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class AbstractApplicationTest extends TestCase
{
    /**
     * Bootstrap.
     *
     * Test that modules will be bootstrapped by application.
     *
     * @covers \ExtendsFramework\Application\AbstractApplication::__construct()
     * @covers \ExtendsFramework\Application\AbstractApplication::bootstrap()
     * @covers \ExtendsFramework\Application\AbstractApplication::triggerOnStartup()
     * @covers \ExtendsFramework\Application\AbstractApplication::triggerOnShutdown()
     */
    public function testBootstrap(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $module = new ModuleBootstrapStub();
        $application = new AbstractApplicationStub($serviceLocator, [
            $module
        ]);
        $application->bootstrap();

        $this->assertTrue($module->isStartup());
        $this->assertTrue($module->isShutdown());
    }
}

class ModuleBootstrapStub implements ModuleInterface, StartupProviderInterface, ShutdownProviderInterface
{
    /**
     * @var bool
     */
    protected $startup = false;

    /**
     * @var bool
     */
    protected $shutdown = false;

    /**
     * @inheritDoc
     */
    public function onStartup(ServiceLocatorInterface $serviceLocator): void
    {
        $this->startup = true;
    }

    /**
     * @inheritDoc
     */
    public function onShutdown(ServiceLocatorInterface $serviceLocator): void
    {
        $this->shutdown = true;
    }

    /**
     * @return bool
     */
    public function isStartup(): bool
    {
        return $this->startup;
    }

    /**
     * @return bool
     */
    public function isShutdown(): bool
    {
        return $this->shutdown;
    }
}

class AbstractApplicationStub extends AbstractApplication
{
    /**
     * @var bool
     */
    protected $called = false;

    /**
     * @return bool
     */
    public function isCalled(): bool
    {
        return $this->called;
    }

    /**
     * @inheritDoc
     */
    protected function run(): AbstractApplication
    {
        $this->called = true;

        return $this;
    }
}
