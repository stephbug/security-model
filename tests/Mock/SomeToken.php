<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\Token;

class SomeToken extends Token
{
    public function __construct(array $roles = [])
    {
        // fixMe error on serialization
        parent::__construct($roles);

        $this->hasRoles() and $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getSecurityKey(): SecurityKey
    {
        return new SomeSecurityKey('foo');
    }
}