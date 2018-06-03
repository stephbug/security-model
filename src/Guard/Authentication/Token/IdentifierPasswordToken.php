<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;

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

        $this->hasRoles() and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getSecurityKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function eraseCredentials(): void
    {
        parent::eraseCredentials();

        $this->credentials = new EmptyCredentials();
    }

    public function serialize(): string
    {
        return serialize([$this->credentials, $this->securityKey, parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->credentials, $this->securityKey, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}