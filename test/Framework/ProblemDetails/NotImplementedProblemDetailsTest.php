<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Request\Uri\UriInterface;
use PHPUnit\Framework\TestCase;

class NotImplementedProblemDetailsTest extends TestCase
{
    /**
     * Getters.
     *
     * Test that getters will return correct values.
     *
     * @covers \ExtendsFramework\Application\Framework\ProblemDetails\NotImplementedProblemDetails::__construct()
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
        $problemDetails = new NotImplementedProblemDetails($request);

        $this->assertSame('/problems/application/not-implemented', $problemDetails->getType());
        $this->assertSame('Not Implemented', $problemDetails->getTitle());
        $this->assertSame('Request cannot be fulfilled.', $problemDetails->getDetail());
        $this->assertSame(501, $problemDetails->getStatus());
        $this->assertSame('/foo/bar', $problemDetails->getInstance());
        $this->assertNull($problemDetails->getAdditional());
    }
}
