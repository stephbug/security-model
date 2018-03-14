<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Application\Values\RecallerKey;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\User\UserSecurity;

class RecallerToken extends Token
{
    /**
     * @var SecurityKey
     */
    private $securityKey;

    /**
     * @var RecallerKey
     */
    private $recallerKey;

    public function __construct(UserSecurity $user, SecurityKey $securityKey, RecallerKey $recallerKey)
    {
        parent::__construct();

        $this->setUser($user);
        $this->securityKey = $securityKey;
        $this->recallerKey = $recallerKey;

        $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getSecurityKey(): SecurityKey
    {
        return $this->securityKey;
    }

    public function getRecallerKey(): RecallerKey
    {
        return $this->recallerKey;
    }

    public function serialize(): string
    {
        return serialize([$this->recallerKey, $this->securityKey, parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->recallerKey, $this->securityKey, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}