<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Module\Provider;

interface ConditionProviderInterface
{
    /**
     * Check if module is conditioned.
     *
     * If module is conditioned, true is returned, it will be skipped for further processing.
     *
     * @return bool
     */
    public function isConditioned(): bool;
}
