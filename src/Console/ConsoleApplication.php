<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\Application\Console\Exception\TaskExecuteFailed;
use ExtendsFramework\Application\Console\Exception\TaskNotFound;
use ExtendsFramework\Application\Console\Exception\TaskParameterMissing;
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
    private $shell;

    /**
     * @inheritDoc
     */
    public function __construct(ShellInterface $shell, ServiceLocatorInterface $serviceLocator, array $modules)
    {
        parent::__construct($serviceLocator, $modules);

        $this->shell = $shell;
    }

    /**
     * @inheritDoc
     * @throws ConsoleException
     */
    protected function run(): AbstractApplication
    {
        $result = $this->shell->process(array_slice($GLOBALS['argv'], 1));
        if ($result instanceof ShellResultInterface) {
            $command = $result->getCommand();
            $parameters = $command->getParameters();
            if (!isset($parameters['task'])) {
                throw new TaskParameterMissing($command);
            }

            try {
                /** @var TaskInterface $task */
                $task = $this
                    ->getServiceLocator()
                    ->getService($parameters['task']);
            } catch (ServiceLocatorException $exception) {
                throw new TaskNotFound($command, $exception);
            }

            try {
                $task->execute($result->getData());
            } catch (TaskException $exception) {
                throw new TaskExecuteFailed($command, $exception);
            }
        }

        return $this;
    }
}
