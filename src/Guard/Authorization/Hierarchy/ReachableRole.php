<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Hierarchy;

use StephBug\SecurityModel\Application\Values\RoleValue;
use StephBug\SecurityModel\Role\RoleSecurity;

class ReachableRole implements RoleHierarchy
{
    /**
     * @var array
     */
    private $map;

    /**
     * @var array
     */
    private $rolesHierarchy;

    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;

        $this->buildRoleMap();
    }

    protected function buildRoleMap(): void
    {
        $this->map = [];
        foreach ($this->rolesHierarchy as $main => $roles) {
            $this->map[$main] = $roles;
            $visited = [];
            $additionalRoles = $roles;
            while ($role = array_shift($additionalRoles)) {
                if (!array_key_exists($role, $this->rolesHierarchy)) {
                    continue;
                }

                $visited[] = $role;
                $this->map[$main] = array_unique(array_merge($this->map[$main], $this->rolesHierarchy[$role]));
                $additionalRoles = array_merge($additionalRoles, array_diff($this->rolesHierarchy[$role], $visited));
            }
        }
    }

    public function getReachableRoles(array $roles): array
    {
        $reachableRoles = $roles;

        /** @var RoleSecurity $role */
        foreach ($roles as $role) {
            $role = $role->getRole();
            if (!array_key_exists($role, $this->map)) {
                continue;
            }

            foreach ((array)$this->map[$role] as $r) {
                $reachableRoles[] = new RoleValue($r);
            }
        }

        return $reachableRoles;
    }
}