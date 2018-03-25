<?php

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Role\RoleValue;
use StephBug\SecurityModel\Role\RoleSecurity;

trait HasRoles
{
    /**
     * @var array
     */
    private $roles;

    public function __construct(array $roles = [])
    {
        foreach ($roles as &$role) {
            if (is_string($role)) {
                $role = new RoleValue($role);
            } elseif (!$role instanceof RoleSecurity) {
                throw InvalidArgument::reason(
                    sprintf('Role must be a string or implement %s contract', RoleSecurity::class)
                );
            }
        }

        if ($this->isClocking()) {
            $this->freshClock();
        }

        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRoles(): bool
    {
        return count($this->roles) > 0;
    }
}