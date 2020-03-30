<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Loader;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Application\Framework\Http\Middleware\InternalServerErrorMiddleware;
use ExtendsFramework\Application\Framework\Http\Middleware\NotImplementedMiddleware;
use ExtendsFramework\Application\Framework\Http\Middleware\RendererMiddleware;
use ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory;
use ExtendsFramework\Application\Http\Renderer\Renderer;
use ExtendsFramework\Application\Http\Renderer\RendererInterface;
use ExtendsFramework\Authentication\Framework\Http\Middleware\UnauthorizedMiddleware;
use ExtendsFramework\Authorization\Framework\Http\Middleware\ForbiddenMiddleware;
use ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware;
use ExtendsFramework\ProblemDetails\Framework\Http\Middleware\ProblemDetailsMiddleware;
use ExtendsFramework\Router\Framework\Http\Middleware\Controller\ControllerMiddleware;
use ExtendsFramework\Router\Framework\Http\Middleware\Router\RouterMiddleware;
use ExtendsFramework\Security\Framework\Http\Middleware\RouterAuthorizationMiddleware;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\Invokable\InvokableResolver;
use ExtendsFramework\ServiceLocator\Resolver\Reflection\ReflectionResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class ApplicationConfigLoader implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return [
            ServiceLocatorInterface::class => [
                FactoryResolver::class => [
                    ApplicationInterface::class => ApplicationFactory::class,
                ],
                InvokableResolver::class => [
                    NotImplementedMiddleware::class => NotImplementedMiddleware::class,
                    InternalServerErrorMiddleware::class => InternalServerErrorMiddleware::class,
                    RendererInterface::class => Renderer::class,
                ],
                ReflectionResolver::class => [
                    RendererMiddleware::class => RendererMiddleware::class,
                ],
            ],
            MiddlewareChainInterface::class => [
                RendererMiddleware::class => 1100,
                ProblemDetailsMiddleware::class => 1000,
                InternalServerErrorMiddleware::class => 900,
                LoggerMiddleware::class => 800,
                HateoasMiddleware::class => 700,
                RouterMiddleware::class => 600,
                ForbiddenMiddleware::class => 500,
                UnauthorizedMiddleware::class => 400,
                RouterAuthorizationMiddleware::class => 300,
                ControllerMiddleware::class => 200,
                NotImplementedMiddleware::class => 100,
            ],
        ];
    }
}
