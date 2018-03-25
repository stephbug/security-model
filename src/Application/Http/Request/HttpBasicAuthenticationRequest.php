<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\User\ClearPassword;
use StephBug\SecurityModel\Application\Values\User\EmailAddress;
use Symfony\Component\HttpFoundation\Request;

class HttpBasicAuthenticationRequest implements AuthenticationRequest
{
    public function extract(IlluminateRequest $request): array
    {
        return [
            EmailAddress::fromString($request->getUser()),
            new ClearPassword($request->getPassword())
        ];
    }

    public function matches(Request $request)
    {
        return null === $request->getUser();
    }
}