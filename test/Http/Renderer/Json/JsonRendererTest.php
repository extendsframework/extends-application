<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http\Renderer\Json;

use ExtendsFramework\Http\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;

class JsonRendererTest extends TestCase
{
    /**
     * Render.
     *
     * Test that response will be rendered: headers sent, body encoded and HTTP status code set.
     *
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::render()
     */
    public function testRender(): void
    {
        Buffer::reset();

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn([
                'foo' => 'bar',
            ]);

        $response
            ->expects($this->exactly(2))
            ->method('andHeader')
            ->withConsecutive(
                ['Content-Type', 'application/json'],
                ['Content-Length', '13']
            )
            ->willReturnSelf();

        $response
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn([
                'Accept' => [
                    'text/html',
                    'application/xhtml+xml',
                    'application/xml;q=0.9',
                    '*/*;q=0.8',
                ],
                'Content-Type' => 'application/json',
                'Content-Length' => '13',
            ]);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        /**
         * @var ResponseInterface $response
         */
        $renderer = new JsonRenderer();

        ob_start();
        $renderer->render($response);
        $output = ob_get_clean();

        $this->assertSame('{"foo":"bar"}', $output);
        $this->assertSame([
            'Accept: text/html, application/xhtml+xml, application/xml;q=0.9, */*;q=0.8',
            'Content-Type: application/json',
            'Content-Length: 13',
        ], Buffer::getHeaders());
        $this->assertSame(200, Buffer::getCode());
    }

    /**
     * Render.
     *
     * Test that no body will be send.
     *
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::render()
     */
    public function testEmptyBody(): void
    {
        Buffer::reset();

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(null);

        $response
            ->expects($this->exactly(2))
            ->method('andHeader')
            ->withConsecutive(
                ['Content-Type', 'application/json'],
                ['Content-Length', '0']
            )
            ->willReturnSelf();

        $response
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn([
                'Accept-Charset' => 'utf-8',
                'Content-Type' => 'application/json',
                'Content-Length' => '0',
            ]);

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        /**
         * @var ResponseInterface $response
         */
        $renderer = new JsonRenderer();

        ob_start();
        $renderer->render($response);
        $output = ob_get_clean();

        $this->assertSame('', $output);
        $this->assertSame([
            'Accept-Charset: utf-8',
            'Content-Type: application/json',
            'Content-Length: 0',
        ], Buffer::getHeaders());
        $this->assertSame(200, Buffer::getCode());
    }
}

/**
 * @param $header
 */
function header($header)
{
    Buffer::addHeader($header);
}

/**
 * @param $code
 */
function http_response_code($code)
{
    Buffer::setCode($code);
}
