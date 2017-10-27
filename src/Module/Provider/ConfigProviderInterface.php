<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Module\Provider;

use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;

interface ConfigProviderInterface
{
    /**
     * Get module config.
     *
     * @return LoaderInterface
     */
    public function getConfig(): LoaderInterface;
}
