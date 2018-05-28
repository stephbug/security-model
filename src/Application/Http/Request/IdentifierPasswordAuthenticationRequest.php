<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\User\ClearPassword;
use Symfony\Component\HttpFoundation\Request;

class IdentifierPasswordAuthenticationRequest implements AuthenticationRequest
{
    /**
     * @var string
     */
    private $loginRoute;

    public function __construct(string $loginRoute)
    {
        $this->loginRoute = $loginRoute;
    }

    public function extract(IlluminateRequest $request): array
    {
        return [
            EmailIdentifier::fromString($request->input('identifier')),
            new ClearPassword($request->input('password'))
        ];
    }

    public function matches(Request $request): bool
    {
        if ($request instanceof IlluminateRequest) {
            return $request->route()->getName() === $this->loginRoute;
        }

        return false;
    }
}