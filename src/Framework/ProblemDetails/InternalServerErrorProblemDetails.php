<?php
declare(strict_types=1);

namespace ExtendsFramework\Application\Framework\ProblemDetails;

use ExtendsFramework\Http\Request\RequestInterface;
use ExtendsFramework\ProblemDetails\ProblemDetails;

class InternalServerErrorProblemDetails extends ProblemDetails
{
    /**
     * InternalServerErrorProblemDetails constructor.
     *
     * @param RequestInterface $request
     * @param string|null $reference
     */
    public function __construct(RequestInterface $request, string $reference = null)
    {
        if (is_string($reference)) {
            $additional = [
                'reference' => $reference,
            ];
        }

        parent::__construct(
            '/problems/application/internal-server-error',
            'Internal Server Error',
            'An unknown error occurred.',
            500,
            $request->getUri()->toRelative(),
            $additional ?? null
        );
    }
}
