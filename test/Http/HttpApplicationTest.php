<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http;

use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class HttpApplicationTest extends TestCase
{
    /**
     * Run.
     *
     * Test that middleware chain will be proceed with request.
     *
     * @covers \ExtendsFramework\Application\Http\HttpApplication::__construct()
     * @covers \ExtendsFramework\Application\Http\HttpApplication::run()
     * @covers \ExtendsFramework\Application\Http\HttpApplication::getRequest()
     * @covers \ExtendsFramework\Application\Http\HttpApplication::getChain()
     */
    public function testRun(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request);

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        /**
         * @var MiddlewareChainInterface $chain
         * @var RequestInterface         $request
         * @var ServiceLocatorInterface  $serviceLocator
         */
        $application = new HttpApplication($chain, $request, $serviceLocator, []);
        $application->bootstrap();
    }
}
