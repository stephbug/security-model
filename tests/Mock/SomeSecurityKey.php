<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class SomeSecurityKey extends SecurityKey
{
    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this;
    }
}