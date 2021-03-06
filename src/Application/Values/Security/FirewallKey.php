<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Security;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;

final class FirewallKey extends SecurityKey
{
    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->key === $aValue->value();
    }
}