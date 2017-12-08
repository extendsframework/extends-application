<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\Http\Middleware;

use Exception;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

class ExceptionMiddlewareTest extends TestCase
{
    /**
     * Process.
     *
     * Test that chain is called with request and response will be returend.
     *
     * @covers \ExtendsFramework\Application\Framework\Http\Middleware\ExceptionMiddleware::process()
     */
    public function testProcess(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willReturn($response);

        /**
         * @var RequestInterface         $request
         * @var MiddlewareChainInterface $chain
         */
        $middleware = new ExceptionMiddleware();

        $this->assertSame($response, $middleware->process($request, $chain));
    }

    /**
     * Caught exception.
     *
     * Test that exception will be caught and a new response will be returned.
     *
     * @covers \ExtendsFramework\Application\Framework\Http\Middleware\ExceptionMiddleware::process()
     */
    public function testCaughtException(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willThrowException(new Exception('Fancy exception message!', 136));

        /**
         * @var RequestInterface         $request
         * @var MiddlewareChainInterface $chain
         */
        $middleware = new ExceptionMiddleware();
        $response = $middleware->process($request, $chain);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        if ($response instanceof ResponseInterface) {
            $this->assertSame(500, $response->getStatusCode());
            $this->assertSame([
                'type' => '',
                'title' => 'Internal server error.',
                'error' => 'Failed to execute request, caught exception with code "136". Please try again.',
            ], $response->getBody());
        }
    }
}
