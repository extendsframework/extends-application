<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Server\Renderer\Json;

use ExtendsFramework\Application\Server\Renderer\RendererInterface;
use ExtendsFramework\Http\Response\ResponseInterface;

class JsonRenderer implements RendererInterface
{
    /**
     * @inheritDoc
     */
    public function render(ResponseInterface $response): void
    {
        $body = $this->stringifyBody($response);
        $response = $this->addHeaders($response, $body);

        $this
            ->sendHeaders($response)
            ->sendResponseCode($response)
            ->sendBody($body);
    }

    /**
     * Send headers.
     *
     * @param ResponseInterface $response
     * @return JsonRenderer
     */
    protected function sendHeaders(ResponseInterface $response): JsonRenderer
    {
        foreach ($response->getHeaders() as $header => $value) {
            if (is_array($value) === true) {
                $value = implode(', ', $value);
            }

            header(sprintf(
                '%s: %s',
                $header,
                $value
            ));
        }

        return $this;
    }

    /**
     * Send response status code.
     *
     * @param ResponseInterface $response
     * @return JsonRenderer
     */
    protected function sendResponseCode(ResponseInterface $response): JsonRenderer
    {
        http_response_code($response->getStatusCode());

        return $this;
    }

    /**
     * Send body.
     *
     * @param string $body
     * @return JsonRenderer
     */
    protected function sendBody(string $body): JsonRenderer
    {
        echo $body;

        return $this;
    }

    /**
     * Stringify response body to JSON string.
     *
     * @param ResponseInterface $response
     * @return string
     */
    protected function stringifyBody(ResponseInterface $response): string
    {
        $body = $response->getBody();
        if ($body === null) {
            return '';
        }

        return json_encode($body, JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    /**
     * Add Content-Length header to response.
     *
     * @param ResponseInterface $response
     * @param string            $body
     * @return ResponseInterface
     */
    protected function addHeaders(ResponseInterface $response, string $body): ResponseInterface
    {
        return $response
            ->andHeader('Content-Type', 'application/json')
            ->andHeader('Content-Length', (string)strlen($body));
    }
}
