<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\AnonymousIdentifier;
use StephBug\SecurityModel\Application\Values\AnonymousKey;
use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Application\Values\SecurityKey;

class AnonymousToken extends Token
{
    /**
     * @var AnonymousKey
     */
    private $anonymousKey;

    public function __construct(AnonymousIdentifier $identifier, AnonymousKey $anonymousKey)
    {
        parent::__construct();

        $this->setUser($identifier);
        $this->anonymousKey = $anonymousKey;

        $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getSecurityKey(): SecurityKey
    {
        return $this->anonymousKey;
    }

    public function serialize(): string
    {
        return serialize([$this->anonymousKey, parent::serialize()]);
    }

    public function unserialize($serialized)
    {
        [$this->anonymousKey, $parentStr] = unserialize($serialized, [Tokenable::class]);

        parent::unserialize($parentStr);
    }
}