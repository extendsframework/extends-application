<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ConfigProviderInterface;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;

class ModuleConfigStub implements ModuleInterface, ConfigProviderInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * ModuleStub constructor.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): LoaderInterface
    {
        return $this->loader;
    }
}
