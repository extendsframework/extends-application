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
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::stringifyBody()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::addHeaders()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::sendHeaders()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::sendResponseCode()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::sendBody()
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
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::stringifyBody()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::addHeaders()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::sendHeaders()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::sendResponseCode()
     * @covers \ExtendsFramework\Application\Http\Renderer\Json\JsonRenderer::sendBody()
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

class Buffer
{
    protected static $headers = [];

    protected static $code;

    public static function getHeaders(): array
    {
        return self::$headers;
    }

    public static function getCode(): int
    {
        return self::$code;
    }

    public static function addHeader(string $header): void
    {
        self::$headers[] = $header;
    }

    public static function setCode($code): void
    {
        self::$code = $code;
    }

    public static function reset(): void
    {
        static::$headers = [];
        static::$code = null;
    }
}

function header($header)
{
    Buffer::addHeader($header);
}

function http_response_code($code)
{
    Buffer::setCode($code);
}
