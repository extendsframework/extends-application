<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\Http\Middleware;

use ExtendsFramework\Application\Framework\ProblemDetails\InternalServerErrorProblemDetails;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Middleware\MiddlewareInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\Http\Response\Response;
use ExtendsFramework\Http\Response\ResponseInterface;
use ExtendsFramework\Logger\Exception\ReferencedExceptionInterface;
use Throwable;

class InternalServerErrorMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(RequestInterface $request, MiddlewareChainInterface $chain): ResponseInterface
    {
        try {
            return $chain->proceed($request);
        } catch (Throwable $throwable) {
            if ($throwable instanceof ReferencedExceptionInterface) {
                $reference = $throwable->getReference();
            }

            return (new Response())->withBody(
                new InternalServerErrorProblemDetails($request, $reference ?? null)
            );
        }
    }
}
