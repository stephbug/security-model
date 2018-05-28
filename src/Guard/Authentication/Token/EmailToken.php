<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;

class EmailToken extends Token
{
    /**
     * @var SecurityKey
     */
    private $securityKey;

    public function __construct(UserToken $user, SecurityKey $securityKey, array $roles = [])
    {
        parent::__construct($roles);

        $this->setUser($user);
        $this->securityKey = $securityKey;

        (count($roles) > 0) and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getSecurityKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function serialize(): string
    {
        return serialize([$this->securityKey, parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->securityKey, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}