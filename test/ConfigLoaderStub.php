<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;

class ConfigLoaderStub implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return [
            'global' => false,
        ];
    }
}
