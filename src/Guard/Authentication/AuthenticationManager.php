<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class AuthenticationManager implements Authenticatable
{
    /**
     * @var AuthenticationProviders
     */
    private $providers;

    public function __construct(AuthenticationProviders $providers)
    {
        $this->providers = $providers;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        return $this->providers->firstSupportedProvider($token)->authenticate($token);
    }
}