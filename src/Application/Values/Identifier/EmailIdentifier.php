<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Exception\Assert\Secure;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier as BaseEmailIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class EmailIdentifier implements BaseEmailIdentifier, SecurityIdentifier, UserToken
{
    /**
     * @var string
     */
    private $email;

    public static function fromString($email): self
    {
        $message = 'Email address is not valid';

        Secure::notEmpty($email, 'Email address can not be empty');
        Secure::string($email, $message);
        Secure::email($email, $message);

        return new self($email);
    }

    private function __construct(string $email)
    {
        $this->email = $email;
    }

    public function identify(): string
    {
        return $this->email;
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->email === $aValue->identify();
    }
}