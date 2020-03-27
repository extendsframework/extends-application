<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use PHPUnit\Framework\TestCase;

class InternalServerErrorProblemDetailsTest extends TestCase
{
    /**
     * Getters.
     *
     * Test that getters will return correct values.
     *
     * @covers \ExtendsFramework\Application\Framework\ProblemDetails\InternalServerErrorProblemDetails::__construct()
     */
    public function testGetters(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('toRelative')
            ->willReturn('/foo/bar');

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        /**
         * @var RequestInterface $request
         */
        $problemDetails = new InternalServerErrorProblemDetails($request, '123');

        $this->assertSame('/problems/application/internal-server-error', $problemDetails->getType());
        $this->assertSame('Internal Server Error', $problemDetails->getTitle());
        $this->assertSame('An unknown error occurred.', $problemDetails->getDetail());
        $this->assertSame(500, $problemDetails->getStatus());
        $this->assertSame('/foo/bar', $problemDetails->getInstance());
        $this->assertSame(['reference' => '123'], $problemDetails->getAdditional());
    }

    /**
     * Without reference.
     *
     * Test that additional information will be empty when no reference is provided.
     *
     * @covers \ExtendsFramework\Application\Framework\ProblemDetails\InternalServerErrorProblemDetails::__construct()
     */
    public function testWithoutReference(): void
    {
        $request = $this->createMock(RequestInterface::class);

        /**
         * @var RequestInterface $request
         */
        $problemDetails = new InternalServerErrorProblemDetails($request);

        $this->assertSame(null, $problemDetails->getAdditional());
    }
}
