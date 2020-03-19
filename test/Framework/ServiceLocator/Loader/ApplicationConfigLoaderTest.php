<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Loader;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Application\Framework\Http\Middleware\ExceptionMiddleware;
use ExtendsFramework\Application\Framework\Http\Middleware\NotImplementedMiddleware;
use ExtendsFramework\Application\Framework\Http\Middleware\RendererMiddleware;
use ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory;
use ExtendsFramework\Application\Http\Renderer\Renderer;
use ExtendsFramework\Application\Http\Renderer\RendererInterface;
use ExtendsFramework\Authentication\Framework\Http\Middleware\NotAuthenticatedMiddleware;
use ExtendsFramework\Authorization\Framework\Http\Middleware\NotAuthorizedMiddleware;
use ExtendsFramework\Hateoas\Framework\Http\Middleware\Hateoas\HateoasMiddleware;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware;
use ExtendsFramework\ProblemDetails\Framework\Http\Middleware\ProblemDetailsMiddleware;
use ExtendsFramework\Router\Framework\Http\Middleware\Controller\ControllerMiddleware;
use ExtendsFramework\Router\Framework\Http\Middleware\Router\RouterMiddleware;
use ExtendsFramework\Security\Framework\Http\Middleware\RouterAuthorizationMiddleware;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\Invokable\InvokableResolver;
use ExtendsFramework\ServiceLocator\Resolver\Reflection\ReflectionResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class ApplicationConfigLoaderTest extends TestCase
{
    /**
     * Load.
     *
     * Test that correct config is loaded.
     *
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Loader\ApplicationConfigLoader::load()
     */
    public function testLoad(): void
    {
        $loader = new ApplicationConfigLoader();

        $this->assertSame([
            ServiceLocatorInterface::class => [
                FactoryResolver::class => [
                    ApplicationInterface::class => ApplicationFactory::class,
                ],
                InvokableResolver::class => [
                    NotImplementedMiddleware::class => NotImplementedMiddleware::class,
                    ExceptionMiddleware::class => ExceptionMiddleware::class,
                    RendererInterface::class => Renderer::class,
                ],
                ReflectionResolver::class => [
                    RendererMiddleware::class => RendererMiddleware::class,
                ],
            ],
            MiddlewareChainInterface::class => [
                RendererMiddleware::class => 1100,
                ExceptionMiddleware::class => 1000,
                LoggerMiddleware::class => 900,
                ProblemDetailsMiddleware::class => 800,
                HateoasMiddleware::class => 700,
                RouterMiddleware::class => 600,
                NotAuthorizedMiddleware::class => 500,
                NotAuthenticatedMiddleware::class => 400,
                RouterAuthorizationMiddleware::class => 300,
                ControllerMiddleware::class => 200,
                NotImplementedMiddleware::class => 100,
            ],
        ], $loader->load());
    }
}
