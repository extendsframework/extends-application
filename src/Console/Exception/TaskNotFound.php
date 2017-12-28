<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console\Exception;

use ExtendsFramework\Application\Console\ConsoleException;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\Shell\Command\CommandInterface;
use InvalidArgumentException;

class TaskNotFound extends InvalidArgumentException implements ConsoleException
{
    /**
     * TaskNotFound constructor.
     *
     * @param CommandInterface        $command
     * @param ServiceLocatorException $exception
     */
    public function __construct(CommandInterface $command, ServiceLocatorException $exception)
    {
        parent::__construct(sprintf(
            'Task for command "%s" can not be found, see previous exception for more details.',
            $command->getName()
        ), 0, $exception);
    }
}
