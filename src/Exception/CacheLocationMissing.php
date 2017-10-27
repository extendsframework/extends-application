<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Exception;

use ExtendsFramework\Application\ApplicationBuilderException;
use RuntimeException;

class CacheLocationMissing extends RuntimeException implements ApplicationBuilderException
{
    /**
     * ConfigLocationMissing constructor.
     */
    public function __construct()
    {
        parent::__construct('Cache location is required and can not be empty.');
    }
}
