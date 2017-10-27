<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Module\Provider;

use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

interface BootstrapProviderInterface
{
    /**
     * Bootstrap module.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function onBootstrap(ServiceLocatorInterface $serviceLocator): void;
}
