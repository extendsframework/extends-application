<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class NotImplementedProblemDetails extends ProblemDetails
{
    /**
     * NotImplementedProblemDetails constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        parent::__construct(
            '/problems/application/not-implemented',
            'Not Implemented',
            'Request cannot be fulfilled.',
            501,
            $request->getUri()->toRelative()
        );
    }
}
