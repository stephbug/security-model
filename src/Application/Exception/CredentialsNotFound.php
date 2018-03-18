<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

class CredentialsNotFound extends AuthenticationServiceException
{
    public static function reason(string $message = null): self
    {
        return new static($message ?? 'No Token found in storage');
    }
}