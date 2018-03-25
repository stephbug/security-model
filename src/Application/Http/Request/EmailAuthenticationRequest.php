<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress as EmailContract;
use StephBug\SecurityModel\Application\Values\User\EmailAddress;
use Symfony\Component\HttpFoundation\Request;

class EmailAuthenticationRequest implements AuthenticationRequest
{
    public function extract(IlluminateRequest $request): EmailContract
    {
        return EmailAddress::fromString($request->input('identifier'));
    }

    public function matches(Request $request): bool
    {
        return $request->isMethod('post') && $request->is('*login');
    }
}