<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\Application\Console\Exception\TaskExecuteFailed;
use ExtendsFramework\Application\Console\Exception\TaskNotFound;
use ExtendsFramework\Application\Console\Exception\TaskParameterMissing;
use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Shell\ShellInterface;
use ExtendsFramework\Shell\ShellResultInterface;
use ExtendsFramework\Shell\Task\TaskException;
use ExtendsFramework\Shell\Task\TaskInterface;

class ConsoleApplication extends AbstractApplication
{
    /**
     * Shell.
     *
     * @var ShellInterface
     */
    protected $shell;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @inheritDoc
     */
    public function __construct(
        ShellInterface $shell,
        ServiceLocatorInterface $serviceLocator,
        array $modules
    ) {
        parent::__construct($serviceLocator, $modules);

        $this->shell = $shell;
    }

    /**
     * @inheritDoc
     */
    protected function run(): AbstractApplication
    {
        $this->process(array_slice($GLOBALS['argv'], 1));

        return $this;
    }

    /**
     * Process $arguments and return task result.
     *
     * @param array $arguments
     * @return void
     * @throws ConsoleException
     */
    protected function process(array $arguments): void
    {
        $result = $this
            ->getShell()
            ->process($arguments);
        if ($result instanceof ShellResultInterface) {
            $command = $result->getCommand();
            $parameters = $command->getParameters();
            if (array_key_exists('task', $parameters) === false) {
                throw new TaskParameterMissing($command);
            }

            try {
                $task = $this->getTask($parameters['task']);
            } catch (ServiceLocatorException $exception) {
                throw new TaskNotFound($command, $exception);
            }

            try {
                $task->execute(
                    $result->getData()
                );
            } catch (TaskException $exception) {
                throw new TaskExecuteFailed($command, $exception);
            }
        }
    }

    /**
     * Get task for $key from service locator.
     *
     * @param string $key
     * @return TaskInterface
     * @throws ServiceLocatorException
     */
    protected function getTask(string $key): object
    {
        return $this
            ->getServiceLocator()
            ->getService($key);
    }

    /**
     * Get shell.
     *
     * @return ShellInterface
     */
    protected function getShell(): ShellInterface
    {
        return $this->shell;
    }
}
