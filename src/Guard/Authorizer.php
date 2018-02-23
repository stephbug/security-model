<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Role\Exception\AuthorizationDenied;

class Authorizer
{
    /**
     * @var Grantable
     */
    private $authorizationChecker;

    public function __construct(Grantable $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function grant(array $attributes, object $object = null): bool
    {
        return $this->authorizationChecker->isGranted($attributes, $object);
    }

    public function requireGranted(array $attributes, object $object = null): bool
    {
        if ($this->grant($attributes, $object)) {
            return true;
        }

        throw AuthorizationDenied::reason();
    }
}