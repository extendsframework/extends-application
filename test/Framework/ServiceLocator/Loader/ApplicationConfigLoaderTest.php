<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Loader;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Application\Framework\Http\Middleware\NotImplementedMiddleware;
use ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory;
use ExtendsFramework\Authentication\Framework\Http\Middleware\NotAuthenticatedMiddleware;
use ExtendsFramework\Authorization\Framework\Http\Middleware\NotAuthorizedMiddleware;
use ExtendsFramework\Http\Framework\Http\Middleware\Exception\ExceptionMiddleware;
use ExtendsFramework\Http\Framework\Http\Middleware\Renderer\RendererMiddleware;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware;
use ExtendsFramework\Router\Framework\Http\Middleware\Controller\ControllerMiddleware;
use ExtendsFramework\Router\Framework\Http\Middleware\Router\RouterMiddleware;
use ExtendsFramework\Security\Framework\Http\Middleware\RouterAuthorizationMiddleware;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\Invokable\InvokableResolver;
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
                ],
            ],
            MiddlewareChainInterface::class => [
                RendererMiddleware::class => 200,
                ExceptionMiddleware::class => 190,
                LoggerMiddleware::class => 175,
                RouterMiddleware::class => 150,
                NotAuthorizedMiddleware::class => 150,
                NotAuthenticatedMiddleware::class => 130,
                RouterAuthorizationMiddleware::class => 120,
                ControllerMiddleware::class => 100,
                NotImplementedMiddleware::class => 90,
            ],
        ], $loader->load());
    }
}
