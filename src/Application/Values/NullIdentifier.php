<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;

class NullIdentifier implements SecurityIdentifier
{

    /**
     * @return mixed
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