<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Exception;

class UserIsLocked extends InvalidUserStatus
{
    public static function isLocked(SecurityIdentifier $identifier): self
    {
        return new static(sprintf('User identified by % is locked', $identifier->identify()));
    }
}