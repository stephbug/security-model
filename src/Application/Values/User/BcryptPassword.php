<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\User;

use StephBug\SecurityModel\Application\Exception\Assert\Secure;
use StephBug\SecurityModel\Application\Values\Contract\EncodedPassword;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;

class BcryptPassword extends Password implements EncodedPassword
{
    const EXACT_LENGTH = 60;

    public function __construct($password)
    {
        parent::__construct($password);
    }

    public function credentials(): string
    {
        return $this->password;
    }

    protected function validate($password): void
    {
        Secure::length($password, self::EXACT_LENGTH, 'Invalid credential');
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->password === $aValue->credentials();
    }
}