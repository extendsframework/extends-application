<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Exception\CacheLocationMissing;
use ExtendsFramework\Application\Exception\FailedToLoadCache;
use ExtendsFramework\Application\Framework\ServiceLocator\Loader\ApplicationConfigLoader;
use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ConditionProviderInterface;
use ExtendsFramework\Application\Module\Provider\ConfigProviderInterface;
use ExtendsFramework\Authentication\Framework\ServiceLocator\Loader\AuthenticationConfigLoader;
use ExtendsFramework\Authorization\Framework\ServiceLocator\Loader\AuthorizationConfigLoader;
use ExtendsFramework\Basic\Merger\MergerException;
use ExtendsFramework\Basic\Merger\MergerInterface;
use ExtendsFramework\Basic\Merger\Recursive\RecursiveMerger;
use ExtendsFramework\Command\Framework\ServiceLocator\Loader\CommandConfigLoader;
use ExtendsFramework\Console\Framework\ServiceLocator\Loader\ConsoleConfigLoader;
use ExtendsFramework\Console\Shell\Framework\ServiceLocator\Loader\ConsoleShellConfigLoader;
use ExtendsFramework\Console\Terminal\Framework\ServiceLocator\Loader\ConsoleTerminalConfigLoader;
use ExtendsFramework\Event\Framework\ServiceLocator\Loader\EventConfigLoader;
use ExtendsFramework\Http\Framework\ServiceLocator\Loader\HttpConfigLoader;
use ExtendsFramework\Http\Router\Framework\ServiceLocator\Loader\HttpRouterConfigLoader;
use ExtendsFramework\Http\Server\Framework\ServiceLocator\Loader\HttpServerConfigLoader;
use ExtendsFramework\Identity\Framework\ServiceLocator\Loader\IdentityConfigLoader;
use ExtendsFramework\Logger\Framework\ServiceLocator\Loader\LoggerConfigLoader;
use ExtendsFramework\Security\Framework\ServiceLocator\Loader\SecurityConfigLoader;
use ExtendsFramework\Serializer\Framework\ServiceLocator\Loader\SerializerConfigLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\Cache\CacheLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\File\FileLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderException;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorFactory;
use ExtendsFramework\ServiceLocator\ServiceLocatorFactoryInterface;
use ExtendsFramework\Sourcing\Framework\ServiceLocator\Loader\SourcingConfigLoader;
use ExtendsFramework\Validator\Framework\ServiceLocator\Loader\ValidatorConfigLoader;

class ApplicationBuilder implements ApplicationBuilderInterface
{
    /**
     * If framework is enabled.
     *
     * @var bool
     */
    protected $frameworkEnabled = true;

    /**
     * Global config paths for glob.
     *
     * @var string[]
     */
    protected $globalConfigPaths = [];

    /**
     * Cache location.
     *
     * @var string|null
     */
    protected $cacheLocation;

    /**
     * Cache filename.
     *
     * @var string|null
     */
    protected $cacheFilename;

    /**
     * If cache is enabled.
     *
     * @var bool|null
     */
    protected $cacheEnabled;

    /**
     * Added modules.
     *
     * @var ModuleInterface[]
     */
    protected $modules = [];

    /**
     * Framework configs.
     *
     * @var LoaderInterface[]
     */
    protected $configs = [];

    /**
     * Config loader.
     *
     * @var LoaderInterface|null
     */
    protected $loader;

    /**
     * Config merger.
     *
     * @var MergerInterface|null
     */
    protected $merger;

    /**
     * Service locator factory.
     *
     * @var ServiceLocatorFactoryInterface
     */
    protected $factory;

    /**
     * Framework configs.
     *
     * @var LoaderInterface[]
     */
    protected $frameworkConfigs = [
        ApplicationConfigLoader::class,
        AuthenticationConfigLoader::class,
        AuthorizationConfigLoader::class,
        SecurityConfigLoader::class,
        IdentityConfigLoader::class,
        ConsoleConfigLoader::class,
        ConsoleTerminalConfigLoader::class,
        ConsoleShellConfigLoader::class,
        HttpConfigLoader::class,
        HttpRouterConfigLoader::class,
        HttpServerConfigLoader::class,
        LoggerConfigLoader::class,
        ValidatorConfigLoader::class,
        SerializerConfigLoader::class,
        CommandConfigLoader::class,
        EventConfigLoader::class,
        SourcingConfigLoader::class,
    ];

    /**
     * @inheritDoc
     */
    public function build(): ApplicationInterface
    {
        try {
            $config = $this->getConfig();
        } catch (ApplicationBuilderException | LoaderException | MergerException $exception) {
            throw new FailedToLoadCache($exception);
        }

        $application = $this
            ->getServiceLocatorFactory()
            ->create($config)
            ->getService(ApplicationInterface::class, [
                'modules' => $this->modules,
            ]);

        $this->reset();

        return $application;
    }

    /**
     * Add global config path for glob.
     *
     * All the added global config paths will be merged in chronological order.
     *
     * @param string[] ...$globalConfigPaths
     * @return ApplicationBuilder
     */
    public function addGlobalConfigPath(string ...$globalConfigPaths): ApplicationBuilder
    {
        foreach ($globalConfigPaths as $globalConfigPath) {
            $this->globalConfigPaths[] = $globalConfigPath;
        }

        return $this;
    }

    /**
     * Add config loader.
     *
     * @param LoaderInterface[] ...$loaders
     * @return ApplicationBuilder
     */
    public function addConfig(LoaderInterface ...$loaders): ApplicationBuilder
    {
        foreach ($loaders as $loader) {
            $this->configs[] = $loader;
        }

        return $this;
    }

    /**
     * @param string $cacheLocation
     * @return ApplicationBuilder
     */
    public function setCacheLocation(string $cacheLocation): ApplicationBuilder
    {
        $this->cacheLocation = $cacheLocation;

        return $this;
    }

    /**
     * @param string $cacheFilename
     * @return ApplicationBuilder
     */
    public function setCacheFilename(string $cacheFilename): ApplicationBuilder
    {
        $this->cacheFilename = $cacheFilename;

        return $this;
    }

    /**
     * Set cache enabled.
     *
     * Cache is disabled by default. Default value is true.
     *
     * @param bool $cacheEnabled
     * @return ApplicationBuilder
     */
    public function setCacheEnabled(bool $cacheEnabled = null): ApplicationBuilder
    {
        $this->cacheEnabled = $cacheEnabled ?? true;

        return $this;
    }

    /**
     * Set framework enabled.
     *
     * Framework is enabled by default. Default value is true.
     *
     * @param bool $frameworkEnabled
     * @return ApplicationBuilder
     */
    public function setFrameworkEnabled(bool $frameworkEnabled = null): ApplicationBuilder
    {
        $this->frameworkEnabled = $frameworkEnabled ?? true;

        return $this;
    }

    /**
     * Add module.
     *
     * @param ModuleInterface[] ...$modules
     * @return ApplicationBuilder
     */
    public function addModule(ModuleInterface ...$modules): ApplicationBuilder
    {
        foreach ($modules as $module) {
            $this->modules[] = $module;
        }

        return $this;
    }

    /**
     * Set service locator factory.
     *
     * @param ServiceLocatorFactoryInterface $factory
     * @return ApplicationBuilder
     */
    public function setServiceLocatorFactory(ServiceLocatorFactoryInterface $factory): ApplicationBuilder
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get merged global and module config.
     *
     * @return array
     * @throws LoaderException
     * @throws MergerException
     * @throws ApplicationBuilderException
     */
    protected function getConfig(): array
    {
        $loader = null;
        if ($this->isCacheEnabled() === true) {
            $loader = $this->getLoader();
            $cached = $loader->load();
            if (empty($cached) === false) {
                return $cached;
            }
        }

        if ($this->isFrameworkEnabled() === true) {
            $this->addFrameworkConfigs();
        }

        $merged = [];
        $merger = $this->getMerger();
        foreach ($this->getConfigs() as $config) {
            $merged = $merger->merge(
                $merged,
                $config->load()
            );
        }

        foreach ($this->getGlobalConfig() as $global) {
            $merged = $merger->merge($merged, $global);
        }

        foreach ($this->getModuleConfig() as $module) {
            $merged = $merger->merge($merged, $module);
        }

        if ($this->isCacheEnabled() === true) {
            if ($loader instanceof CacheLoader) {
                $loader->save($merged);
            }
        }

        return $merged;
    }

    /**
     * Add framework configs.
     *
     * @return ApplicationBuilder
     */
    protected function addFrameworkConfigs(): ApplicationBuilder
    {
        foreach ($this->getFrameworkConfigs() as $loader) {
            $loader = new $loader;
            if ($loader instanceof LoaderInterface) {
                $this->addConfig($loader);
            }
        }

        return $this;
    }

    /**
     * Get config from modules.
     *
     * @return array
     * @throws LoaderException
     * @throws MergerException
     */
    protected function getModuleConfig(): array
    {
        $merged = [];
        $merger = $this->getMerger();
        foreach ($this->getModules() as $module) {
            if ($module instanceof ConditionProviderInterface && $module->isConditioned() === true) {
                continue;
            }

            if ($module instanceof ConfigProviderInterface) {
                $merged = $merger->merge(
                    $merged,
                    $module->getConfig()->load()
                );
            }
        }

        return $merged;
    }

    /**
     * Get global config.
     *
     * @return array
     * @throws LoaderException
     */
    protected function getGlobalConfig(): array
    {
        $loader = new FileLoader();
        foreach ($this->globalConfigPaths as $path) {
            $loader->addPath($path);
        }

        return $loader->load();
    }

    /**
     * Get config loaders.
     *
     * @return LoaderInterface[]
     */
    protected function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * Get cache location.
     *
     * @return string
     * @throws ApplicationBuilderException
     */
    protected function getCacheLocation(): string
    {
        if ($this->cacheLocation === null) {
            throw new CacheLocationMissing();
        }

        return $this->cacheLocation;
    }

    /**
     * Get cache filename without extension.
     *
     * @return string
     */
    protected function getCacheFilename(): string
    {
        return $this->cacheFilename ?? 'config.cache';
    }

    /**
     * Get framework configs.
     *
     * @return LoaderInterface[]
     */
    protected function getFrameworkConfigs(): array
    {
        return $this->frameworkConfigs;
    }

    /**
     * Get cache enabled.
     *
     * @return bool
     */
    protected function isCacheEnabled(): bool
    {
        return $this->cacheEnabled ?? false;
    }

    /**
     * If framework is enabled.
     *
     * @return bool
     */
    protected function isFrameworkEnabled(): bool
    {
        return $this->frameworkEnabled;
    }

    /**
     * Get enabled modules.
     *
     * @return ModuleInterface[]
     */
    protected function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Get service locator factory.
     *
     * @return ServiceLocatorFactoryInterface
     */
    protected function getServiceLocatorFactory(): ServiceLocatorFactoryInterface
    {
        if (!$this->factory instanceof ServiceLocatorFactoryInterface) {
            $this->factory = new ServiceLocatorFactory();
        }

        return $this->factory ?: new ServiceLocatorFactory();
    }

    /**
     * Get global config loader.
     *
     * @return LoaderInterface
     * @throws ApplicationBuilderException
     */
    protected function getLoader(): LoaderInterface
    {
        if (!$this->loader instanceof LoaderInterface) {
            $this->loader = new CacheLoader(sprintf(
                '%s/%s.php',
                rtrim($this->getCacheLocation(), '/'),
                $this->getCacheFilename()
            ));
        }

        return $this->loader;
    }

    /**
     * Get config merger.
     *
     * @return MergerInterface
     */
    protected function getMerger(): MergerInterface
    {
        if (!$this->merger instanceof MergerInterface) {
            $this->merger = new RecursiveMerger();
        }

        return $this->merger;
    }

    /**
     * Reset builder.
     */
    protected function reset(): void
    {
        $this->frameworkEnabled = true;
        $this->globalConfigPaths = [];
        $this->cacheLocation = null;
        $this->cacheFilename = null;
        $this->cacheEnabled = null;
        $this->modules = [];
        $this->configs = [];
        $this->loader = null;
        $this->merger = null;
        $this->factory = null;
    }
}
