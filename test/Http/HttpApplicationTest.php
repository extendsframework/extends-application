<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http;

use ExtendsFramework\Http\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class HttpApplicationTest extends TestCase
{
    /**
     * Run.
     *
     * Test that server will run.
     *
     * @covers \ExtendsFramework\Application\Http\HttpApplication::__construct()
     * @covers \ExtendsFramework\Application\Http\HttpApplication::run()
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
        $application = new HttpApplication($server, $serviceLocator, []);
        $application->bootstrap();
    }
}
