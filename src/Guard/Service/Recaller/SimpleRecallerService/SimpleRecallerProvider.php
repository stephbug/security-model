<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller\SimpleRecallerService;

use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier;
use StephBug\SecurityModel\User\Recaller\UserRecallerProviderRead;
use StephBug\SecurityModel\User\Recaller\UserRecallerProviderStore;
use StephBug\SecurityModel\User\UserRecaller;
use StephBug\SecurityModel\User\UserSecurity;

class SimpleRecallerProvider implements UserRecallerProviderRead, UserRecallerProviderStore
{
    public function __construct()
    {

    }

    public function requireByRecallerIdentifier(RecallerIdentifier $identifier): UserSecurity
    {

    }

    public function refreshUserWithRecallerIdentifier(UserRecaller $user,
                                                      RecallerIdentifier $identifier): UserSecurity
    {
        // TODO: Implement refreshUserWithRecallerIdentifier() method.
    }
}