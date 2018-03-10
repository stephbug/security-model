<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Hierarchy;

interface RoleHierarchy
{
    public function getReachableRoles(array $roles): array;
}