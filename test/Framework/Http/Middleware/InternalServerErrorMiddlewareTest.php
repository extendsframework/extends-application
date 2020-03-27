<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\Http\Middleware;

use Exception;
use ExtendsFramework\Application\Framework\ProblemDetails\InternalServerErrorProblemDetails;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Logger\Exception\ReferencedExceptionInterface;
use PHPUnit\Framework\TestCase;

class InternalServerErrorMiddlewareTest extends TestCase
{
    /**
     * Process.
     *
     * Test that chain is called with request and response will be returned.
     *
     * @covers \ExtendsFramework\Application\Framework\Http\Middleware\InternalServerErrorMiddleware::process()
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
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         */
        $middleware = new InternalServerErrorMiddleware();

        $this->assertSame($response, $middleware->process($request, $chain));
    }

    /**
     * Exception.
     *
     * Test that exception will be caught and a new response will be returned.
     *
     * @covers \ExtendsFramework\Application\Framework\Http\Middleware\InternalServerErrorMiddleware::process()
     */
    public function testException(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willThrowException(new Exception('Fancy exception message!', 136));

        /**
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         */
        $middleware = new InternalServerErrorMiddleware();
        $response = $middleware->process($request, $chain);

        $problemDetails = $response->getBody();
        $this->assertInstanceOf(InternalServerErrorProblemDetails::class, $problemDetails);
        $this->assertNull($problemDetails->getAdditional());
    }

    /**
     * Referenced exception.
     *
     * Test that referenced exception will be caught and a new response will be returned.
     *
     * @covers \ExtendsFramework\Application\Framework\Http\Middleware\InternalServerErrorMiddleware::process()
     */
    public function testReferencedException(): void
    {
        $request = $this->createMock(RequestInterface::class);

        $exception = $this->createMock(ReferencedExceptionInterface::class);
        $exception
            ->expects($this->once())
            ->method('getReference')
            ->willReturn('123');

        $chain = $this->createMock(MiddlewareChainInterface::class);
        $chain
            ->expects($this->once())
            ->method('proceed')
            ->with($request)
            ->willThrowException($exception);

        /**
         * @var RequestInterface $request
         * @var MiddlewareChainInterface $chain
         */
        $middleware = new InternalServerErrorMiddleware();
        $response = $middleware->process($request, $chain);

        $problemDetails = $response->getBody();
        $this->assertInstanceOf(InternalServerErrorProblemDetails::class, $problemDetails);
        $this->assertSame(['reference' => '123'], $problemDetails->getAdditional());
    }
}
