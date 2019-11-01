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
    private function triggerOnStartup(): AbstractApplication
    {
        foreach ($this->getModules() as $module) {
            if ($module instanceof StartupProviderInterface) {
                $module->onStartup(
                    $this->getServiceLocator()
                );
            }
        }

        return $this;
    }

    /**
     * Trigger shutdown providers.
     *
     * @return AbstractApplication
     */
    private function triggerOnShutdown(): AbstractApplication
    {
        foreach ($this->getModules() as $module) {
            if ($module instanceof ShutdownProviderInterface) {
                $module->onShutdown(
                    $this->getServiceLocator()
                );
            }
        }

        return $this;
    }

    /**
     * Get modules.
     *
     * @return ModuleInterface[]
     */
    private function getModules(): array
    {
        return $this->modules;
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
