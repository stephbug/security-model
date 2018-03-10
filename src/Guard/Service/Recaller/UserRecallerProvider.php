<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller;

use StephBug\SecurityModel\User\UserSecurity;

interface UserRecallerProvider
{
    public function requireByRecallerToken(string $recallerToken): UserSecurity;

    public function refreshRecaller(UserSecurity $user, string $newRecallerToken): UserSecurity;
}