<?php

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Values\RoleValue;
use StephBug\SecurityModel\Role\RoleSecurity;

trait HasRoles
{
    /**
     * @var Collection
     */
    private $roles;

    public function __construct(iterable $roles = [])
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

        $this->roles = new Collection($roles);
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function hasRoles(): bool
    {
        return count($this->roles) > 0;
    }
}