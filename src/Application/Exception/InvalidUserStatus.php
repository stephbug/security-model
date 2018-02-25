<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

use StephBug\SecurityModel\User\UserSecurity;

class InvalidUserStatus extends AuthenticationException
{
    public static function notEnabled(UserSecurity $user): self
    {
        return new static(sprintf('User with id %s is not enabled', $user->getId()->identify()));
    }

    public static function isLocked(UserSecurity $user): self
    {
        return new static(sprintf('User with id %s is locked', $user->getId()->identify()));
    }
}