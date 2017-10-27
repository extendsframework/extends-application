<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Exception\CacheLocationMissing;
use ExtendsFramework\Application\Exception\FailedToLoadCache;
use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ConfigProviderInterface;
use ExtendsFramework\ServiceLocator\Config\Loader\Cache\CacheLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\File\FileLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderException;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\Config\Merger\MergerException;
use ExtendsFramework\ServiceLocator\Config\Merger\MergerInterface;
use ExtendsFramework\ServiceLocator\Config\Merger\Recursive\RecursiveMerger;
use ExtendsFramework\ServiceLocator\ServiceLocator;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class ApplicationBuilder implements ApplicationBuilderInterface
{
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
     * @inheritDoc
     */
    public function build(): ApplicationInterface
    {
        try {
            $config = $this->getConfig();
        } catch (ApplicationBuilderException | LoaderException | MergerException $exception) {
            throw new FailedToLoadCache($exception);
        }

        $application = new Application(
            $this->getServiceLocator($config),
            $this->modules
        );

        $this->reset();

        return $application;
    }

    /**
     * Add global config path for glob.
     *
     * All the added global config paths will be merged in chronological order.
     *
     * @param string $globalConfigPath
     * @return ApplicationBuilder
     */
    public function addGlobalConfigPath(string $globalConfigPath): ApplicationBuilder
    {
        $this->globalConfigPaths[] = $globalConfigPath;

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
     * Add module.
     *
     * When $enabled is false, the module won't be added. This comes in handy when a module can only be enabled  in
     * specific situations, like a administrative module for console only. Default value for $enabled is true.
     *
     * @param ModuleInterface $module
     * @param bool|null       $enabled
     * @return ApplicationBuilder
     */
    public function addModule(ModuleInterface $module, bool $enabled = null): ApplicationBuilder
    {
        if ($enabled ?? true) {
            $this->modules[] = $module;
        }

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

        $merged = [];
        $merger = $this->getMerger();
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
     * Get config from modules.
     *
     * @return array
     * @throws LoaderException
     */
    protected function getModuleConfig(): array
    {
        $loaded = [];
        foreach ($this->getModules() as $module) {
            if ($module instanceof ConfigProviderInterface) {
                $loaded[] = $module->getConfig()->load();
            }
        }

        return $loaded;
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
     * Get cache filename.
     *
     * @return string
     */
    protected function getCacheFilename(): string
    {
        return $this->cacheFilename ?? 'config.cache.php';
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
     * Get enabled modules.
     *
     * @return ModuleInterface[]
     */
    protected function getModules(): array
    {
        return $this->modules;
    }

    /**
     * Get service locator.
     *
     * @param array $config
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator(array $config): ServiceLocatorInterface
    {
        return new ServiceLocator($config);
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
        $this->globalConfigPaths = [];
        $this->cacheLocation = null;
        $this->cacheFilename = null;
        $this->cacheEnabled = null;
        $this->modules = [];
        $this->loader = null;
        $this->merger = null;
    }
}
