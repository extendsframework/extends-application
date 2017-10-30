<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Module\Provider;

use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

interface StartupProviderInterface
{
    /**
     * Module startup.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function onStartup(ServiceLocatorInterface $serviceLocator): void;
}
