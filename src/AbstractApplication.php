<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\BootstrapProviderInterface;
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
        foreach ($this->modules as $module) {
            if ($module instanceof BootstrapProviderInterface) {
                $module->onBootstrap($this->serviceLocator);
            }
        }

        $this->run();
    }

    /**
     * Run application.
     */
    abstract protected function run(): void;
}
