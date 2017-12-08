<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Terminal;

use ExtendsFramework\Console\Terminal\TerminalInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class TerminalApplicationTest extends TestCase
{
    /**
     * Run.
     *
     * Test that server will run.
     *
     * @covers \ExtendsFramework\Application\Terminal\TerminalApplication::__construct()
     * @covers \ExtendsFramework\Application\Terminal\TerminalApplication::run()
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
        $application = new TerminalApplication($terminal, $serviceLocator, []);
        $application->bootstrap();
    }
}
