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
    protected $modules;

    /**
     * Service locator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

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
        $this
            ->triggerOnStartup()
            ->run()
            ->triggerOnShutdown();
    }

    /**
     * Trigger startup providers.
     *
     * @return AbstractApplication
     */
    protected function triggerOnStartup(): AbstractApplication
    {
        foreach ($this->modules as $module) {
            if ($module instanceof StartupProviderInterface) {
                $module->onStartup($this->serviceLocator);
            }
        }

        return $this;
    }

    /**
     * Trigger shutdown providers.
     *
     * @return AbstractApplication
     */
    protected function triggerOnShutdown(): AbstractApplication
    {
        foreach ($this->modules as $module) {
            if ($module instanceof ShutdownProviderInterface) {
                $module->onShutdown($this->serviceLocator);
            }
        }

        return $this;
    }

    /**
     * Run application.
     *
     * @return AbstractApplication
     */
    abstract protected function run(): AbstractApplication;
}
