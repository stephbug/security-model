<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Role\Exception;

use StephBug\SecurityModel\Application\Exception\AuthorizationException;
use StephBug\SecurityModel\Application\Exception\SecurityException;

class AuthorizationDenied extends AuthorizationException
{
    public static function reason(string $message = null, SecurityException $exception = null): self
    {
        return new static($message ?? 'Authorization denied', 0, $exception);
    }
}