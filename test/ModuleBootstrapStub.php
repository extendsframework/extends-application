<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ShutdownProviderInterface;
use ExtendsFramework\Application\Module\Provider\StartupProviderInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class ModuleBootstrapStub implements ModuleInterface, StartupProviderInterface, ShutdownProviderInterface
{
    /**
     * @var bool
     */
    protected $startup = false;

    /**
     * @var bool
     */
    protected $shutdown = false;

    /**
     * @inheritDoc
     */
    public function onStartup(ServiceLocatorInterface $serviceLocator): void
    {
        $this->startup = true;
    }

    /**
     * @inheritDoc
     */
    public function onShutdown(ServiceLocatorInterface $serviceLocator): void
    {
        $this->shutdown = true;
    }

    /**
     * @return bool
     */
    public function isStartup(): bool
    {
        return $this->startup;
    }

    /**
     * @return bool
     */
    public function isShutdown(): bool
    {
        return $this->shutdown;
    }
}
