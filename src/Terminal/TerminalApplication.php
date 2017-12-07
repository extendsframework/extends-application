<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Terminal;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Terminal\TerminalInterface;

class TerminalApplication extends AbstractApplication
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
