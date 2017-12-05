<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console;

use ExtendsFramework\Terminal\TerminalInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class ConsoleApplicationTest extends TestCase
{
    /**
     * Run.
     *
     * Test that server will run.
     *
     * @covers \ExtendsFramework\Application\Console\ConsoleApplication::__construct()
     * @covers \ExtendsFramework\Application\Console\ConsoleApplication::run()
     */
    public function testRun(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $terminal = $this->createMock(TerminalInterface::class);
        $terminal
            ->expects($this->once())
            ->method('run');

        /**
         * @var TerminalInterface       $terminal
         * @var ServiceLocatorInterface $serviceLocator
         */
        $application = new ConsoleApplication($terminal, $serviceLocator, []);
        $application->bootstrap();
    }
}
