<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Security;

use StephBug\SecurityModel\Application\Exception\Assert\Secure;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;

abstract class SecurityKey implements SecurityValue
{
    /**
     * @var string
     */
    protected $key;

    public function __construct($key)
    {
        $this->validateKey($key);

        $this->key = $key;
    }

    protected function validateKey($key): void
    {
        Secure::string($key);
        Secure::notEmpty($key);
    }

    public function value(): string
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->key;
    }
}