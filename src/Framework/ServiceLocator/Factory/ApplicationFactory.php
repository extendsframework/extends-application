<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Factory;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Application\Console\ConsoleApplication;
use ExtendsFramework\Application\Http\HttpApplication;
use ExtendsFramework\Terminal\TerminalInterface;
use ExtendsFramework\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\ServiceFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class ApplicationFactory implements ServiceFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): ApplicationInterface
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
    protected function getConsoleApplication(ServiceLocatorInterface $serviceLocator): ConsoleApplication
    {
        return new ConsoleApplication(
            $serviceLocator->getService(TerminalInterface::class),
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
    protected function getHttpApplication(ServiceLocatorInterface $serviceLocator): HttpApplication
    {
        return new HttpApplication(
            $serviceLocator->getService(ServerInterface::class),
            $serviceLocator,
            $extra['modules'] ?? []
        );
    }
}
