<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Http;

use ExtendsFramework\Application\AbstractApplication;
use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class HttpApplication extends AbstractApplication
{
    /**
     * Middleware chain.
     *
     * @var MiddlewareChainInterface
     */
    protected $chain;

    /**
     * Request.
     *
     * @var RequestInterface
     */
    protected $request;

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
     */
    protected function run(): AbstractApplication
    {
        $this
            ->getChain()
            ->proceed(
                $this->getRequest()
            );

        return $this;
    }

    /**
     * Get middleware chain.
     *
     * @return MiddlewareChainInterface
     */
    protected function getChain(): MiddlewareChainInterface
    {
        return $this->chain;
    }

    /**
     * Get request.
     *
     * @return RequestInterface
     */
    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
