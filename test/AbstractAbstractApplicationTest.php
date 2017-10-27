<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\BootstrapProviderInterface;
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

        $this->assertTrue($module->isCalled());
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
    protected function run(): void
    {
        $this->called = true;
    }
}
