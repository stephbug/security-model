<?php

return [

    'guard' => [

    ],

    'authorizer' => [
        'role_prefix' => 'ROLE_',

        'role_hierarchy' => [
            'service' => \StephBug\SecurityModel\Guard\Authorization\Hierarchy\ReachableRole::class,

            'roles' => [
                /*'ROLE_ADMIN' => ['ROLE_USER']*/
            ]
        ],

        'strategy' => \StephBug\SecurityModel\Guard\Authorization\Strategy\UnanimousStrategy::class,

        'grant' => \StephBug\SecurityModel\Guard\Authorization\AuthorizationChecker::class,

        'voters' => [
            \StephBug\SecurityModel\Guard\Authorization\Voter\AuthenticatedTokenVoter::class,
            \StephBug\SecurityModel\Guard\Authorization\Hierarchy\RoleHierarchy::class,
        ]
    ]
];