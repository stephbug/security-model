<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class IdentifierPasswordAuthenticationFirewall extends GenericAuthenticationFirewall
{
    protected function createToken(Request $request): Tokenable
    {
        [$identifier, $credential] = $this->authenticationRequest->extract($request);

        return new IdentifierPasswordToken($identifier, $credential);
    }
}