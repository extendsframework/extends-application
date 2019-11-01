<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ShutdownProviderInterface;
use ExtendsFramework\Application\Module\Provider\StartupProviderInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * Application modules.
     *
     * @var ModuleInterface[]
     */
    private $modules;

    /**
     * Service locator.
     *
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * AbstractApplication constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param ModuleInterface[]       $modules
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, array $modules)
    {
        $this->modules = $modules;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof StartupProviderInterface) {
                $module->onStartup($this->getServiceLocator());
            }
        }

        $this->run();

        foreach ($this->modules as $module) {
            if ($module instanceof ShutdownProviderInterface) {
                $module->onShutdown($this->getServiceLocator());
            }
        }
    }

    /**
     * Get service locator.
     *
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator(): ServiceLocatorInterface
    {
        return $this->serviceLocator;
    }

    /**
     * Run application.
     *
     * @return AbstractApplication
     */
    abstract protected function run(): AbstractApplication;
}
