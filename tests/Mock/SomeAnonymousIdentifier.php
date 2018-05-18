<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class SomeAnonymousIdentifier implements SecurityIdentifier, UserToken
{
    public function identify(): string
    {
        return '';
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return false;
    }
}