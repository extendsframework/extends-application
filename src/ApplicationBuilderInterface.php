<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

interface ApplicationBuilderInterface
{
    /**
     * Build application.
     *
     * @return ApplicationInterface
     * @throws ApplicationBuilderException
     */
    public function build(): ApplicationInterface;
}
