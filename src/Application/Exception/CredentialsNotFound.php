<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

class CredentialsNotFound extends AuthenticationServiceException
{
    public static function reason(): self
    {
        return new static('No Token found in storage');
    }
}