<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\Http\Middleware;

use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use PHPUnit\Framework\TestCase;

class NotImplementedMiddlewareTest extends TestCase
{
    /**
     * Process.
     *
     * Test that a 501 response will be returned.
     *
     * @covers \ExtendsFramework\Application\Framework\Http\Middleware\NotImplementedMiddleware::process()
     */
    public function testProcess(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->never())
            ->method('proceed');

        /**
         * @var RequestInterface         $request
         * @var MiddlewareChainInterface $chain
         */
        $middleware = new NotImplementedMiddleware();
        $response = $middleware->process($request, $chain);

        $this->assertSame(501, $response->getStatusCode());
    }
}
