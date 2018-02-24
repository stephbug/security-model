<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Hierarchy\RoleHierarchy;

class RoleHierarchyVoter extends RoleVoter
{
    /**
     * @var RoleHierarchy
     */
    private $roleHierarchy;

    public function __construct(RoleHierarchy $roleHierarchy, string $rolePrefix = 'ROLE_')
    {
        parent::__construct($rolePrefix);

        $this->roleHierarchy = $roleHierarchy;
    }

    protected function extractRoles(Tokenable $token): array
    {
        return $this->roleHierarchy->getReachableRoles($token->getRoles()->all());
    }
}