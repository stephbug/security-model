<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\User\ClearPassword;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicAuthenticationRequest implements AuthenticationRequest
{
    public function extract(IlluminateRequest $request): array
    {
        return [
            EmailIdentifier::fromString($request->getUser()),
            new ClearPassword($request->getPassword())
        ];
    }

    public function matches(Request $request): bool
    {
        return null === $request->getUser();
    }
}