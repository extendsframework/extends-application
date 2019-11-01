<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http\Renderer\Json;

use ExtendsFramework\Application\Http\Renderer\RendererInterface;
use ExtendsFramework\Http\Response\ResponseInterface;

class JsonRenderer implements RendererInterface
{
    /**
     * @inheritDoc
     */
    public function render(ResponseInterface $response): void
    {
        $body = $response->getBody();
        if ($body === null) {
            $body = '';
        } else {
            $body = json_encode($body, JSON_PARTIAL_OUTPUT_ON_ERROR);
        }

        $response
            ->andHeader('Content-Type', 'application/json')
            ->andHeader('Content-Length', (string)strlen($body));

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

        echo $body;
    }
}
