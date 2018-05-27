<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Recaller;

use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier;
use StephBug\SecurityModel\User\UserRecaller;
use StephBug\SecurityModel\User\UserSecurity;

interface UserRecallerProviderStore
{
    public function refreshUserWithRecallerIdentifier(
        UserRecaller $recaller,
        RecallerIdentifier $identifier): UserSecurity;
}