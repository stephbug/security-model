<?php

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Application\Values\RoleValue;
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
                throw new \RuntimeException(
                    sprintf('Role must be a string or implement %s contract', RoleValue::class)
                );
            }
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