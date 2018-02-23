<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

use StephBug\SecurityModel\User\UserSecurity;

class UnsupportedUser extends AuthenticationServiceException
{
    public static function withUser(UserSecurity $user): UnsupportedUser
    {
        return new static(sprintf('No user provider supports user class "%s"', get_class($user)));
    }
}