<?php

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\User\UserSecurity;

trait HasUserChanged
{
    private function requireSupportedUser(UserToken $user): UserToken
    {
        if (!$user instanceof UserSecurity && !$user instanceof SecurityIdentifier) {
            throw InvalidArgument::reason(
                sprintf('User of token must implement %s or %s',
                    UserSecurity::class,
                    SecurityIdentifier::class)
            );
        }

        if ($user instanceof UserSecurity) {
            // has changed
        }

        return $user;
    }

    private function hasChanged(UserSecurity $user): bool
    {
        return false;
    }
}