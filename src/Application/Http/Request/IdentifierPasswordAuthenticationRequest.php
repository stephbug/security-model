<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\User\ClearPassword;
use Symfony\Component\HttpFoundation\Request;

class IdentifierPasswordAuthenticationRequest implements AuthenticationRequest
{
    public function extract(IlluminateRequest $request): array
    {
        return [
            EmailIdentifier::fromString($request->input('identifier')),
            new ClearPassword($request->input('password'))
        ];
    }

    public function matches(Request $request): bool
    {
        return $request->is('*login') && $request->isMethod('post');
    }
}