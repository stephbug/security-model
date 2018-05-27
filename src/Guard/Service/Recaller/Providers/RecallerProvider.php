<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller\Providers;

use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier as BaseIdentifier;
use StephBug\SecurityModel\User\UserSecurity;

interface RecallerProvider
{
    public function requireUserFromRecaller(BaseIdentifier $identifier): UserSecurity;

    public function refreshUserRecaller(UserSecurity $user, BaseIdentifier $identifier): UserSecurity;
}