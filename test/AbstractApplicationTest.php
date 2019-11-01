<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

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
     * @covers \ExtendsFramework\Application\AbstractApplication::getServiceLocator()
     */
    public function testBootstrap(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $module = new ModuleBootstrapStub();
        $application = new AbstractApplicationStub($serviceLocator, [
            $module,
        ]);
        $application->bootstrap();

        $this->assertTrue($module->isStartup());
        $this->assertTrue($module->isShutdown());
    }
}
