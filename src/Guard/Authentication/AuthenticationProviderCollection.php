<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Guard\Authentication\Providers\AuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Providers\NullAuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class AuthenticationProviderCollection
{
    /**
     * @var Collection
     */
    private $providers;

    public function __construct(array $providers = null)
    {
        $this->providers = new Collection($providers ?? []);
    }

    public function add(AuthenticationProvider $provider): self
    {
        $this->providers->push($provider);

        return $this;
    }

    public function firstSupportedProvider(Tokenable $token): AuthenticationProvider
    {
        $providers = $this->providers;

        return $providers
            ->push(new NullAuthenticationProvider())
            ->first(function (AuthenticationProvider $provider) use ($token) {
                return $provider->supports($token);
            });
    }
}