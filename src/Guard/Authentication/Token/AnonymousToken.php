<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\AnonymousIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;

class AnonymousToken extends Token
{
    public function __construct(AnonymousIdentifier $identifier)
    {
        parent::__construct();

        $this->setUser($identifier);

        $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }
}