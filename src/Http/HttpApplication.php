<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Middleware\MiddlewareException;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class HttpApplication extends AbstractApplication
{
    /**
     * Middleware chain.
     *
     * @var MiddlewareChainInterface
     */
    private $chain;

    /**
     * Request.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * @inheritDoc
     */
    public function __construct(
        MiddlewareChainInterface $chain,
        RequestInterface $request,
        ServiceLocatorInterface $serviceLocator,
        array $modules
    ) {
        parent::__construct($serviceLocator, $modules);

        $this->chain = $chain;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     * @throws MiddlewareException
     */
    protected function run(): AbstractApplication
    {
        $this->chain->proceed($this->request);

        return $this;
    }
}
