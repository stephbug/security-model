<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Exception\UnsupportedUser;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\User\UserProvider;
use StephBug\SecurityModel\User\UserSecurity;

class NullUserProvider implements UserProvider
{
    public function refreshUser(UserSecurity $user): UserSecurity
    {
        throw UnsupportedUser::withUser($user);
    }

    public function requireByIdentifier(SecurityIdentifier $identifier): UserSecurity
    {
        throw InvalidArgument::reason(
            sprintf('Method "%s" of class "%s" should never be called.', __METHOD__, get_class($this))
        );
    }

    public function supportsClass(string $userClass): bool
    {
        return true;
    }
}