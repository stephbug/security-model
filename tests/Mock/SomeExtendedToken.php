<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

class SomeExtendedToken extends SomeToken
{
    public function __construct(SomeIdentifier $identifier, array $roles = [])
    {
        $this->clock = false;

        $this->setUser($identifier);

        parent::__construct($roles);
    }
}