<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

interface ApplicationInterface
{
    /**
     * Run application.
     *
     * @throws ApplicationException
     */
    public function bootstrap(): void;
}
