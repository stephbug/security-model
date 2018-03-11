<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class UnsupportedProvider extends AuthenticationServiceException
{
    public static function withSupport(Tokenable $token, Authenticatable $provider = null): self
    {
        if ($provider) {
            $message = 'Authentication provider "%s" does not support token "%s"';

            return new self(sprintf($message, get_class($provider), get_class($token)));
        }

        return new self(sprintf('No authentication provider supports token %s', get_class($token)));
    }
}