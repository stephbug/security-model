<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Exception;

use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;

class UserNotFound extends AuthenticationException
{
    public static function hideBadCredentials(AuthenticationException $exception): UserNotFound
    {
        return new static('User not found', 0, $exception);
    }

    public static function withUniqueId(UniqueIdentifier $uniqueId): self
    {
        return new static(sprintf('User not found with id %s', $uniqueId->identify()));
    }

    public static function withEmail(EmailAddress $emailAddress): self
    {
        return new static(sprintf('User not found with email %s', $emailAddress->identify()));
    }
}