<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Factory;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Application\Terminal\TerminalApplication;
use ExtendsFramework\Application\Server\ServerApplication;
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
     * @return TerminalApplication
     * @throws ServiceLocatorException
     *
     */
    protected function getConsoleApplication(ServiceLocatorInterface $serviceLocator): TerminalApplication
    {
        return new TerminalApplication(
            $serviceLocator->getService(TerminalInterface::class),
            $serviceLocator,
            $extra['modules'] ?? []
        );
    }

    /**
     * Get HTTP application.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return ServerApplication
     * @throws ServiceLocatorException
     */
    protected function getHttpApplication(ServiceLocatorInterface $serviceLocator): ServerApplication
    {
        return new ServerApplication(
            $serviceLocator->getService(ServerInterface::class),
            $serviceLocator,
            $extra['modules'] ?? []
        );
    }
}
