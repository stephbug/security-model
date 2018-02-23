<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\AnonymousIdentifier;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class AnonymousAuthenticationProvider implements AuthenticationProvider
{
    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        return new AnonymousToken(new AnonymousIdentifier());
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof AnonymousToken;
    }
}