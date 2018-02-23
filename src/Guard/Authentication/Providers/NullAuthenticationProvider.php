<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class NullAuthenticationProvider implements AuthenticationProvider
{
    public function authenticate(Tokenable $token): Tokenable
    {
        throw UnsupportedProvider::withSupport($token);
    }

    public function supports(Tokenable $token): bool
    {
        return true;
    }
}