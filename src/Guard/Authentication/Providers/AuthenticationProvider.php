<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface AuthenticationProvider extends Authenticatable
{
    public function supports(Tokenable $token): bool;
}