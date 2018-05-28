<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier as EmailId;
use Symfony\Component\HttpFoundation\Request;

class EmailAuthenticationRequest implements AuthenticationRequest
{
    /**
     * @var string
     */
    private $loginRoute;

    public function __construct(string $loginRoute)
    {
        $this->loginRoute = $loginRoute;
    }

    public function extract(IlluminateRequest $request): EmailIdentifier
    {
        return EmailId::fromString($request->input('identifier'));
    }

    public function matches(Request $request): bool
    {
        if ($request instanceof IlluminateRequest) {
            return $request->route()->getName() === $this->loginRoute;
        }

        return false;
    }
}