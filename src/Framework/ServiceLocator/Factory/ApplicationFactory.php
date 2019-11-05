<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Factory;

use ExtendsFramework\Application\Console\ConsoleApplication;
use ExtendsFramework\Application\Http\HttpApplication;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\ServiceFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\ShellInterface;

class ApplicationFactory implements ServiceFactoryInterface
{
    /**
     * @inheritDoc
     * @throws ServiceLocatorException
     */
    public function createService(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        /** @noinspection ConstantCanBeUsedInspection */
        if (php_sapi_name() === 'cli') {
            return $this->getConsoleApplication($serviceLocator);
        }

        return $this->getHttpApplication($serviceLocator);
    }

    /**
     * Get console application.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ConsoleApplication
     * @throws ServiceLocatorException
     */
    private function getConsoleApplication(ServiceLocatorInterface $serviceLocator): ConsoleApplication
    {
        /** @noinspection PhpParamsInspection */
        return new ConsoleApplication(
            $serviceLocator->getService(ShellInterface::class),
            $serviceLocator,
            $extra['modules'] ?? []
        );
    }

    /**
     * Get HTTP application.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return HttpApplication
     * @throws ServiceLocatorException
     */
    private function getHttpApplication(ServiceLocatorInterface $serviceLocator): HttpApplication
    {
        /** @noinspection PhpParamsInspection */
        return new HttpApplication(
            $serviceLocator->getService(MiddlewareChainInterface::class),
            $serviceLocator->getService(RequestInterface::class),
            $serviceLocator,
            $extra['modules'] ?? []
        );
    }
}
