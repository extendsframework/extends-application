<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\Http\Middleware;

use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Middleware\MiddlewareInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\Response;
use ExtendsFramework\Http\Response\ResponseInterface;
use Throwable;

class ExceptionMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(RequestInterface $request, MiddlewareChainInterface $chain): ResponseInterface
    {
        try {
            return $chain->proceed($request);
        } catch (Throwable $throwable) {
            return (new Response())
                ->withStatusCode(500)
                ->withBody([
                    'type' => '',
                    'title' => 'Internal server error.',
                    'error' => sprintf(
                        'Failed to execute request, caught exception with code "%d". Please try again.',
                        $throwable->getCode()
                    ),
                ]);
        }
    }
}
