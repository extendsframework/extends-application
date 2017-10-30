<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Module\Provider;

use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

interface ShutdownProviderInterface
{
    /**
     * Module shutdown.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function onShutdown(ServiceLocatorInterface $serviceLocator): void;
}
