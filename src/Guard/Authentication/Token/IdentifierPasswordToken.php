<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\SecurityKey;

class IdentifierPasswordToken extends Token
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var SecurityKey
     */
    private $securityKey;

    public function __construct(UserToken $user, Credentials $credentials, SecurityKey $securityKey, array $roles = [])
    {
        parent::__construct($roles);

        $this->setUser($user);
        $this->credentials = $credentials;
        $this->securityKey = $securityKey;

        count($roles) > 0 and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getSecurityKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function toArray(): array
    {
        return array_merge(
            [$this->credentials->credentials(), $this->securityKey->value()],
            parent::toArray()
        );
    }
}