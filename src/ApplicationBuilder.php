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
use ExtendsFramework\Command\Framework\ServiceLocator\Loader\CommandConfigLoader;
use ExtendsFramework\Console\Framework\ServiceLocator\Loader\ConsoleConfigLoader;
use ExtendsFramework\Event\Framework\ServiceLocator\Loader\EventConfigLoader;
use ExtendsFramework\Http\Framework\ServiceLocator\Loader\HttpConfigLoader;
use ExtendsFramework\Identity\Framework\ServiceLocator\Loader\IdentityConfigLoader;
use ExtendsFramework\JsonApi\Framework\ServiceLocator\Loader\JsonApiConfigLoader;
use ExtendsFramework\Logger\Framework\ServiceLocator\Loader\LoggerConfigLoader;
use ExtendsFramework\Merger\MergerException;
use ExtendsFramework\Merger\MergerInterface;
use ExtendsFramework\Merger\Recursive\RecursiveMerger;
use ExtendsFramework\Router\Framework\ServiceLocator\Loader\RouterConfigLoader;
use ExtendsFramework\Security\Framework\ServiceLocator\Loader\SecurityConfigLoader;
use ExtendsFramework\Serializer\Framework\ServiceLocator\Loader\SerializerConfigLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\Cache\CacheLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\File\FileLoader;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderException;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorFactory;
use ExtendsFramework\ServiceLocator\ServiceLocatorFactoryInterface;
use ExtendsFramework\Shell\Framework\ServiceLocator\Loader\ShellConfigLoader;
use ExtendsFramework\Sourcing\Framework\ServiceLocator\Loader\SourcingConfigLoader;
use ExtendsFramework\Validator\Framework\ServiceLocator\Loader\ValidatorConfigLoader;

class ApplicationBuilder implements ApplicationBuilderInterface
{
    /**
     * If framework is enabled.
     *
     * @var bool
     */
    private $frameworkEnabled = true;

    /**
     * Global config paths for glob.
     *
     * @var string[]
     */
    private $globalConfigPaths = [];

    /**
     * Cache location.
     *
     * @var string|null
     */
    private $cacheLocation;

    /**
     * Cache filename.
     *
     * @var string|null
     */
    private $cacheFilename;

    /**
     * If cache is enabled.
     *
     * @var bool|null
     */
    private $cacheEnabled;

    /**
     * Added modules.
     *
     * @var ModuleInterface[]
     */
    private $modules = [];

    /**
     * Framework configs.
     *
     * @var LoaderInterface[]
     */
    private $configs = [];

    /**
     * Config loader.
     *
     * @var LoaderInterface|null
     */
    private $loader;

    /**
     * Config merger.
     *
     * @var MergerInterface|null
     */
    private $merger;

    /**
     * Service locator factory.
     *
     * @var ServiceLocatorFactoryInterface
     */
    private $factory;

    /**
     * Framework configs.
     *
     * @var LoaderInterface[]
     */
    private $frameworkConfigs = [
        ApplicationConfigLoader::class,
        AuthenticationConfigLoader::class,
        AuthorizationConfigLoader::class,
        SecurityConfigLoader::class,
        IdentityConfigLoader::class,
        ConsoleConfigLoader::class,
        ShellConfigLoader::class,
        HttpConfigLoader::class,
        RouterConfigLoader::class,
        LoggerConfigLoader::class,
        ValidatorConfigLoader::class,
        SerializerConfigLoader::class,
        CommandConfigLoader::class,
        EventConfigLoader::class,
        SourcingConfigLoader::class,
        JsonApiConfigLoader::class,
    ];

    /**
     * ApplicationBuilder constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @inheritDoc
     * @throws ServiceLocatorException
     */
    public function build(): ApplicationInterface
    {
        try {
            $config = $this->getConfig();
        } catch (ApplicationBuilderException | LoaderException | MergerException $exception) {
            throw new FailedToLoadCache($exception);
        }

        $application = ($this->factory ?: new ServiceLocatorFactory())
            ->create($config)
            ->getService(ApplicationInterface::class, [
                'modules' => $this->modules,
            ]);

        $this->reset();

        /**
         * @var ApplicationInterface $application
         */
        return $application;
    }

    /**
     * Add global config path for glob.
     *
     * All the added global config paths will be merged in chronological order.
     *
     * @param string ...$globalConfigPaths
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
     * @param LoaderInterface ...$loaders
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
     * Cache is disabled by default.
     *
     * @param bool $cacheEnabled
     * @return ApplicationBuilder
     */
    public function setCacheEnabled(bool $cacheEnabled): ApplicationBuilder
    {
        $this->cacheEnabled = $cacheEnabled;

        return $this;
    }

    /**
     * Set framework enabled.
     *
     * Framework is enabled by default.
     *
     * @param bool $frameworkEnabled
     * @return ApplicationBuilder
     */
    public function setFrameworkEnabled(bool $frameworkEnabled): ApplicationBuilder
    {
        $this->frameworkEnabled = $frameworkEnabled;

        return $this;
    }

    /**
     * Add module.
     *
     * @param ModuleInterface ...$modules
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
    private function getConfig(): array
    {
        if ($this->cacheEnabled) {
            if ($this->loader === null) {
                if ($this->cacheLocation === null) {
                    throw new CacheLocationMissing();
                }

                $this->loader = new CacheLoader(sprintf(
                    '%s/%s.php',
                    rtrim($this->cacheLocation, '/'),
                    $this->cacheFilename
                ));
            }

            $cached = $this->loader->load();
            if (!empty($cached)) {
                return $cached;
            }
        }

        if ($this->frameworkEnabled) {
            foreach ($this->frameworkConfigs as $frameworkConfig) {
                $frameworkConfig = new $frameworkConfig;
                if ($frameworkConfig instanceof LoaderInterface) {
                    $this->addConfig($frameworkConfig);
                }
            }
        }

        $merged = [];
        foreach ($this->configs as $config) {
            $merged = $this->merger->merge(
                $merged,
                $config->load()
            );
        }

        $fileLoader = new FileLoader();
        foreach ($this->globalConfigPaths as $path) {
            $fileLoader->addPath($path);
        }

        foreach ($fileLoader->load() as $global) {
            $merged = $this->merger->merge($merged, $global);
        }

        foreach ($this->modules as $module) {
            if ($module instanceof ConditionProviderInterface && $module->isConditioned()) {
                continue;
            }

            if ($module instanceof ConfigProviderInterface) {
                foreach ($module
                             ->getConfig()
                             ->load() as $loaded) {
                    $merged = $this->merger->merge(
                        $merged,
                        $loaded
                    );
                }
            }
        }

        if ($this->loader instanceof CacheLoader && $this->cacheEnabled) {
            $this->loader->save($merged);
        }

        return $merged;
    }

    /**
     * Reset builder.
     */
    private function reset(): void
    {
        $this->frameworkEnabled = true;
        $this->globalConfigPaths = [];
        $this->cacheLocation = null;
        $this->cacheFilename = 'config.cache';
        $this->cacheEnabled = false;
        $this->modules = [];
        $this->configs = [];
        $this->loader = null;
        $this->merger = new RecursiveMerger();
        $this->factory = null;
    }
}
