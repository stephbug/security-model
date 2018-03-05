<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Role\Exception\AuthorizationDenied;

class Authorizer
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var Grantable
     */
    private $authorizationChecker;

    public function __construct(Guard $guard, Grantable $authorizationChecker)
    {
        $this->guard = $guard;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function grant(array $attributes, $object = null): bool
    {
        $token = $this->guard->requireToken();

        return $this->authorizationChecker->isGranted($token, $attributes, $object ?? request());
    }

    public function requireGranted(array $attributes, $object = null): bool
    {
        if ($this->grant($attributes, $object)) {
            return true;
        }

        throw AuthorizationDenied::reason();
    }
}