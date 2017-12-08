<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Server;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\Http\Server\ServerInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class ServerApplication extends AbstractApplication
{
    /**
     * Http server.
     *
     * @var ServerInterface
     */
    protected $server;

    /**
     * @inheritDoc
     */
    public function __construct(ServerInterface $server, ServiceLocatorInterface $serviceLocator, array $modules)
    {
        parent::__construct($serviceLocator, $modules);

        $this->server = $server;
    }

    /**
     * @inheritDoc
     */
    protected function run(): AbstractApplication
    {
        $this->server->run();

        return $this;
    }
}
