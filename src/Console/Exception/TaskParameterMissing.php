<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console\Exception;

use ExtendsFramework\Application\Console\ConsoleException;
use ExtendsFramework\Shell\Command\CommandInterface;
use InvalidArgumentException;

class TaskParameterMissing extends InvalidArgumentException implements ConsoleException
{
    /**
     * TaskParameterMissing constructor.
     *
     * @param CommandInterface $command
     */
    public function __construct(CommandInterface $command)
    {
        parent::__construct(sprintf(
            'Task parameter not defined for command "%s".',
            $command->getName()
        ));
    }
}
