<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console;

use Exception;
use ExtendsFramework\Shell\Task\TaskException;

class TaskExceptionStub extends Exception implements TaskException
{
}
