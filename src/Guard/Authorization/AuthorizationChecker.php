<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Strategy\AuthorizationStrategy;
use StephBug\SecurityModel\Guard\Guard;

class AuthorizationChecker implements Grantable
{
    /**
     * @var Guard
     */
    private $guard;


    /**
     * @var AuthorizationStrategy
     */

    private $strategy;

    /**
     * @var bool
     */
    private $alwaysAuthenticate = false;

    public function __construct(Guard $guard, AuthorizationStrategy $strategy)
    {
        $this->guard = $guard;
        $this->strategy = $strategy;
    }

    public function isGranted(array $attributes = null, object $object = null): bool
    {
        $token = $this->requireAuthentication($this->guard->requireToken());

        return $this->strategy->decide($token, $attributes ?? [], $object);
    }

    protected function requireAuthentication(Tokenable $token): Tokenable
    {
        if ($this->alwaysAuthenticate || !$token->isAuthenticated()) {
            $this->guard->put(
                $token = $this->guard->authenticate($token)
            );
        }

        return $token;
    }

    public function forceAuthentication(bool $force): Grantable
    {
        $this->alwaysAuthenticate = $force;

        return $this;
    }
}