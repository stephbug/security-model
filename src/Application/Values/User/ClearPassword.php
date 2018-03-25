<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\User;

use StephBug\SecurityModel\Application\Exception\Assert\Secure;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;

class ClearPassword extends Password
{
    const MIN_LENGTH = 8;
    const MAX_LENGTH = 255;

    protected function validate($password): void
    {
        Secure::string($password, 'Password is invalid.');

        $message = sprintf('Password must be between %s and %s', self::MIN_LENGTH, self::MAX_LENGTH);

        Secure::betweenLength($password, self::MIN_LENGTH, self::MAX_LENGTH, $message);
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->password === $aValue->credentials();
    }

    public function credentials(): string
    {
        return $this->password;
    }
}