<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class NullIdentifier implements SecurityIdentifier, UserToken
{

    /**
     * @return null
     */
    public function identify()
    {
        return null;
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->identify() === $aValue->identify();
    }
}