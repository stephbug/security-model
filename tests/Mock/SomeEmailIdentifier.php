<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class SomeEmailIdentifier implements EmailIdentifier, SecurityIdentifier, UserToken
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->email === $aValue->getEmail();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function identify(): string
    {
        return $this->email;
    }
}