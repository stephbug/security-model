<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;

class InvalidArgument extends AuthenticationServiceException
{
    public static function unknownIdentifier($identifier): self
    {
        return new static(
            sprintf('Unknown identifier or unsupported identifier %s',
                $identifier instanceof SecurityIdentifier
                    ? $identifier->identify()
                    : gettype($identifier)
            ));
    }

    public static function reason(string $message, \Throwable $exception = null): self
    {
        return new self($message, 0, $exception);
    }
}