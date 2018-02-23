<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface TrustResolver
{
    public function isAnonymous(Tokenable $token = null): bool;

    public function isFullyAuthenticated(Tokenable $token = null): bool;
}