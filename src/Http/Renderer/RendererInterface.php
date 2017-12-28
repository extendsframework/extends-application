<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http\Renderer;

use ExtendsFramework\Http\Response\ResponseInterface;

interface RendererInterface
{
    /**
     * Render $response.
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function render(ResponseInterface $response): void;
}
