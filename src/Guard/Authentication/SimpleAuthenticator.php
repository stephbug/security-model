<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication;

use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\UserProvider;

interface SimpleAuthenticator
{
    public function authenticateToken(Tokenable $token, UserProvider $userProvider, SecurityKey $securityKey): Tokenable;

    public function supportsToken(Tokenable $token, SecurityKey $securityKey): bool;
}