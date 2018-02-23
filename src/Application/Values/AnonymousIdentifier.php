<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class AnonymousIdentifier implements SecurityIdentifier, UserToken
{
    public function identify(): string
    {
        return 'anon';
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
       return $aValue instanceof $this && $this->identify() === $aValue->identify();
    }
}