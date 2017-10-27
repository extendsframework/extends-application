<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\BootstrapProviderInterface;
use ExtendsFramework\Console\Terminal\TerminalInterface;
use ExtendsFramework\Http\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class Application implements ApplicationInterface
{
    /**
     * Service locator.
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Modules.
     *
     * @var ModuleInterface[]
     */
    protected $modules;

    /**
     * Application constructor.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param ModuleInterface[]       $modules
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, array $modules)
    {
        $this->serviceLocator = $serviceLocator;
        $this->modules = $modules;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof BootstrapProviderInterface) {
                $module->onBootstrap($this->serviceLocator);
            }
        }

        if (php_sapi_name() === 'cli') {
            $this
                ->getTerminal()
                ->run();
        } else {
            $this
                ->getServer()
                ->run();
        }
    }

    /**
     * Get console terminal.
     *
     * @return TerminalInterface
     * @throws ServiceLocatorException
     */
    protected function getTerminal(): TerminalInterface
    {
        return $this->serviceLocator->getService(TerminalInterface::class);
    }

    /**
     * Get HTTP server.
     *
     * @return ServerInterface
     * @throws ServiceLocatorException
     */
    protected function getServer(): ServerInterface
    {
        return $this->serviceLocator->getService(ServerInterface::class);
    }
}
