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

        return new IdentifierPasswordToken($identifier, $credential, $this->securityKey);
    }

    protected function requireAuthentication(Request $request): bool
    {
        /**
         * checkMe
         * Override to let an authenticated remember token re authenticate
         * For better grain, we probably need the trust resolver
         */
        return $this->authenticationRequest->matches($request);
    }
}