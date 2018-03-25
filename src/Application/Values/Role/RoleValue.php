<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Role;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Role\RoleSecurity;

class RoleValue implements SecurityValue, RoleSecurity
{
    /**
     * @var string
     */
    private $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function __toString(): string
    {
        return $this->role;
    }

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this && $this->role === $aValue->getRole();
    }
}