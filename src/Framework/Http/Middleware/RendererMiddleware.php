<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\Http\Middleware;

use ExtendsFramework\Application\Server\Renderer\RendererInterface;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Middleware\MiddlewareInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\ResponseInterface;

class RendererMiddleware implements MiddlewareInterface
{
    /**
     * Renderer
     *
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * RendererMiddleware constructor.
     *
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @inheritDoc
     */
    public function process(RequestInterface $request, MiddlewareChainInterface $chain): ResponseInterface
    {
        $response = $chain->proceed($request);
        $this->renderer->render($response);

        return $response;
    }
}
