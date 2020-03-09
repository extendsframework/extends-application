<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http\Renderer;

use ExtendsFramework\Http\Response\ResponseInterface;

class Renderer implements RendererInterface
{
    /**
     * @inheritDoc
     */
    public function render(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $header => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            header(sprintf(
                '%s: %s',
                $header,
                $value
            ));
        }

        http_response_code($response->getStatusCode());

        echo $response->getBody();
    }
}
