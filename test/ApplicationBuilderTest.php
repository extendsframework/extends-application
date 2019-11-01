<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class ApplicationBuilderTest extends TestCase
{
    /**
     * Build.
     *
     * Test that builder will load and cache config and build an instance of ApplicationInterface.
     *
     * @covers \ExtendsFramework\Application\ApplicationBuilder::__construct()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::addConfig()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::addGlobalConfigPath()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setCacheLocation()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setCacheFilename()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setCacheEnabled()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setServiceLocatorFactory()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::addModule()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::build()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::getConfig()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setFrameworkEnabled()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::reset()
     */
    public function testBuild(): void
    {
        $cacheFile = __DIR__ . '/config/application.cache.php';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $loader = $this->createMock(LoaderInterface::class);
        $loader
            ->expects($this->once())
            ->method('load')
            ->willReturn([
                'enabled' => true,
            ]);

        /**
         * @var LoaderInterface                $loader
         * @var ServiceLocatorFactoryInterface $factory
         */
        $builder = new ApplicationBuilder();
        $application = $builder
            ->setFrameworkEnabled(true)
            ->addConfig(new ConfigLoaderStub())
            ->addGlobalConfigPath(__DIR__ . '/config/global/{,*.}{global,local}.php')
            ->addGlobalConfigPath(__DIR__ . '/config/local/{,*.}{global,local}.php')
            ->setCacheLocation(__DIR__ . '/config/')
            ->setCacheFilename('application.cache')
            ->setCacheEnabled(true)
            ->addModule(new ModuleConfigStub($loader))
            ->addModule(new ModuleConditionedStub())
            ->build();

        $this->assertInstanceOf(ApplicationInterface::class, $application);

        $file = __DIR__ . '/config/application.cache.php';
        if (file_exists($file)) {
            $config = include $file;

            $this->assertArrayHasKey(ServiceLocatorInterface::class, $config);
            $this->assertArrayHasKey(MiddlewareChainInterface::class, $config);
            $this->assertArrayHasKey('local', $config);
            $this->assertArrayHasKey('foo', $config);
            $this->assertArrayHasKey('enabled', $config);
        }

        unlink($cacheFile);
    }

    /**
     * Cached.
     *
     * Test that cached config is returned.
     *
     * @covers \ExtendsFramework\Application\ApplicationBuilder::__construct()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setCacheFilename()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setCacheEnabled()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::setServiceLocatorFactory()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::addModule()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::build()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::getConfig()
     * @covers \ExtendsFramework\Application\ApplicationBuilder::reset()
     */
    public function testCached(): void
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader
            ->expects($this->never())
            ->method('load');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(ApplicationInterface::class)
            ->willReturn($this->createMock(ApplicationInterface::class));

        $factory = $this->createMock(ServiceLocatorFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('create')
            ->willReturn($serviceLocator);

        /**
         * @var LoaderInterface                $loader
         * @var ServiceLocatorFactoryInterface $factory
         */
        $builder = new ApplicationBuilder();
        $application = $builder
            ->setCacheLocation(__DIR__ . '/config/global/')
            ->setCacheEnabled(true)
            ->setCacheFilename('fake.global')
            ->setServiceLocatorFactory($factory)
            ->addModule(new ModuleConfigStub($loader))
            ->addModule(new ModuleConditionedStub())
            ->build();

        $this->assertInstanceOf(ApplicationInterface::class, $application);
    }

    /**
     * Cache location missing.
     *
     * Test that an exception is thrown when cache location is missing.
     *
     * @covers                   \ExtendsFramework\Application\ApplicationBuilder::__construct()
     * @covers                   \ExtendsFramework\Application\ApplicationBuilder::setCacheEnabled()
     * @covers                   \ExtendsFramework\Application\ApplicationBuilder::build()
     * @covers                   \ExtendsFramework\Application\ApplicationBuilder::getConfig()
     * @covers                   \ExtendsFramework\Application\Exception\CacheLocationMissing::__construct
     * @covers                   \ExtendsFramework\Application\Exception\FailedToLoadCache::__construct
     * @expectedException        \ExtendsFramework\Application\Exception\FailedToLoadCache
     * @expectedExceptionMessage Failed to load config. See previous exception for more details.
     */
    public function testCacheLocationMissing(): void
    {
        $builder = new ApplicationBuilder();
        $builder
            ->setCacheEnabled(true)
            ->build();
    }
}
