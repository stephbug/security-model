<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier as EmailId;
use Symfony\Component\HttpFoundation\Request;

class EmailAuthenticationRequest implements AuthenticationRequest
{
    public function extract(IlluminateRequest $request): EmailIdentifier
    {
        return EmailId::fromString($request->input('identifier'));
    }

    public function matches(Request $request): bool
    {
        return $request->isMethod('post') && $request->is('*login');
    }
}