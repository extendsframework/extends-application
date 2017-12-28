<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console\Exception;

use ExtendsFramework\Application\Console\ConsoleException;
use ExtendsFramework\Shell\Command\CommandInterface;
use ExtendsFramework\Shell\Task\TaskException;
use InvalidArgumentException;

class TaskExecuteFailed extends InvalidArgumentException implements ConsoleException
{
    /**
     * TaskExecuteFailed constructor.
     *
     * @param CommandInterface $command
     * @param TaskException    $exception
     */
    public function __construct(CommandInterface $command, TaskException $exception)
    {
        parent::__construct(sprintf(
            'Failed to execute task for command "%s", see previous exception for more details.',
            $command->getName()
        ), 0, $exception);
    }
}
