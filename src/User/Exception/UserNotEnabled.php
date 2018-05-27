<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Exception;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;

class UserNotEnabled extends InvalidUserStatus
{
    public static function notEnabled(SecurityIdentifier $identifier): self
    {
        return new static(sprintf('User identified by % is not enabled', $identifier->identify()));
    }
}