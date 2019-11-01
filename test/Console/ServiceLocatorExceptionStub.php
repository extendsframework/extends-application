<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Console;

use Exception;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;

class ServiceLocatorExceptionStub extends Exception implements ServiceLocatorException
{
}
