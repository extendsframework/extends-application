<?php
declare(strict_types=1);

namespace ExtendsFramework\Application;

use ExtendsFramework\Application\Module\ModuleInterface;
use ExtendsFramework\Application\Module\Provider\ConditionProviderInterface;
use ExtendsFramework\Application\Module\Provider\ConfigProviderInterface;
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use LogicException;

class ModuleConditionedStub implements ModuleInterface, ConfigProviderInterface, ConditionProviderInterface
{
    /**
     * @inheritDoc
     */
    public function isConditioned(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): LoaderInterface
    {
        throw new LogicException('Can not load config from conditioned module.');
    }
}
