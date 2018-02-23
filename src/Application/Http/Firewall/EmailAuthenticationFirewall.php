<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class EmailAuthenticationFirewall extends GenericAuthenticationFirewall
{
    protected function createToken(Request $request): Tokenable
    {
        $identifier = $this->authenticationRequest->extract($request);

        return new EmailToken($identifier, new EmptyCredentials());
    }
}