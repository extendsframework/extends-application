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
     */
    public function createService(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
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
     *
     */
    private function getConsoleApplication(ServiceLocatorInterface $serviceLocator): ConsoleApplication
    {
        $shell = $serviceLocator->getService(ShellInterface::class);

        /**
         * @var ShellInterface $shell
         */
        return new ConsoleApplication(
            $shell,
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
        $chain = $serviceLocator->getService(MiddlewareChainInterface::class);
        $request = $serviceLocator->getService(RequestInterface::class);

        /**
         * @var MiddlewareChainInterface $chain
         * @var RequestInterface         $request
         */
        return new HttpApplication(
            $chain,
            $request,
            $serviceLocator,
            $extra['modules'] ?? []
        );
    }
}
