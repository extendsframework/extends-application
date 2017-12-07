<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Server;

use ExtendsFramework\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class HttpApplicationTest extends TestCase
{
    /**
     * Run.
     *
     * Test that server will run.
     *
     * @covers \ExtendsFramework\Application\Server\ServerApplication::__construct()
     * @covers \ExtendsFramework\Application\Server\ServerApplication::run()
     */
    public function testRun(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $server = $this->createMock(ServerInterface::class);
        $server
            ->expects($this->once())
            ->method('run');

        /**
         * @var ServerInterface         $server
         * @var ServiceLocatorInterface $serviceLocator
         */
        $application = new ServerApplication($server, $serviceLocator, []);
        $application->bootstrap();
    }
}
