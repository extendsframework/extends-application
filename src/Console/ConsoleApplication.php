<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\Terminal\TerminalInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class ConsoleApplication extends AbstractApplication
{
    /**
     * Console terminal.
     *
     * @var TerminalInterface
     */
    protected $terminal;

    /**
     * @inheritDoc
     */
    public function __construct(TerminalInterface $terminal, ServiceLocatorInterface $serviceLocator, array $modules)
    {
        parent::__construct($serviceLocator, $modules);

        $this->terminal = $terminal;
    }

    /**
     * @inheritDoc
     */
    protected function run(): AbstractApplication
    {
        $this->terminal->run();

        return $this;
    }
}
