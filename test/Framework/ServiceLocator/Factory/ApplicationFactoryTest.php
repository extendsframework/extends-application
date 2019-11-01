<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ServiceLocator\Factory;

use ExtendsFramework\Application\ApplicationInterface;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\ShellInterface;
use PHPUnit\Framework\TestCase;

class ApplicationFactoryTest extends TestCase
{
    /**
     * Create HTTP application.
     *
     * Test that factory will create an instance of ApplicationInterface.
     *
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::createService()
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::getHttpApplication()
     */
    public function testCreateHttpApplication(): void
    {
        Buffer::setSapi('cgi');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->exactly(2))
            ->method('getService')
            ->withConsecutive(
                [MiddlewareChainInterface::class],
                [RequestInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                $this->createMock(MiddlewareChainInterface::class),
                $this->createMock(RequestInterface::class)
            );

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $factory = new ApplicationFactory();
        $application = $factory->createService(ApplicationInterface::class, $serviceLocator, [
            'modules' => [],
        ]);

        $this->assertInstanceOf(ApplicationInterface::class, $application);

        Buffer::reset();
    }

    /**
     * Create console application.
     *
     * Test that factory will create an instance of ApplicationInterface.
     *
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::createService()
     * @covers \ExtendsFramework\Application\Framework\ServiceLocator\Factory\ApplicationFactory::getConsoleApplication()
     */
    public function testCreateConsoleApplication(): void
    {
        Buffer::setSapi('cli');

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(ShellInterface::class)
            ->willReturn($this->createMock(ShellInterface::class));

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $factory = new ApplicationFactory();
        $application = $factory->createService(ApplicationInterface::class, $serviceLocator, [
            'modules' => [],
        ]);

        $this->assertInstanceOf(ApplicationInterface::class, $application);

        Buffer::reset();
    }
}

/**
 * @return string
 */
function php_sapi_name(): string
{
    return Buffer::getSapi();
}
