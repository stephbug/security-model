<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Exception;

use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;

class BadCredentials extends AuthenticationException
{
    public static function hasChanged(): self
    {
        return new static('Credentials were changed from another session');
    }

    public static function invalid($credentials = null): self
    {
        $message = 'Invalid credentials';

        if ($credentials instanceof EmptyCredentials) {
            $message = 'Credentials can not be empty';
        }

        return new static($message);
    }
}