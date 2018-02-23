<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Contract;

interface SecurityValue
{
    public function sameValueAs(SecurityValue $aValue): bool;
}