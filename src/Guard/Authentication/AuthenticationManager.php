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

    /**
     * @var bool
     */
    private $eraseCredentials;

    public function __construct(AuthenticationProviders $providers, bool $eraseCredentials = true)
    {
        $this->providers = $providers;
        $this->eraseCredentials = $eraseCredentials;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        $token = $this->providers->firstSupportedProvider($token)->authenticate($token);

        if ($this->eraseCredentials) {
            $token->eraseCredentials();
        }

        return $token;
    }
}