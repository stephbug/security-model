<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;

class SomeIdentifier implements SecurityIdentifier, UserToken
{
    private $id;
    private $sameValueAs;

    public function __construct(string $id, bool $sameValueAs = true)
    {
        $this->id = $id;
        $this->sameValueAs = $sameValueAs;
    }

    public function identify(): string
    {
       return $this->id;
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $this->sameValueAs;
    }
}