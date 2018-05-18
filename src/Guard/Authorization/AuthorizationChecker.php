<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Strategy\AuthorizationStrategy;
use StephBug\SecurityModel\Guard\Contract\Guardable;

class AuthorizationChecker implements Grantable
{
    /**
     * @var Guardable
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

    public function __construct(Guardable $guard, AuthorizationStrategy $strategy)
    {
        $this->guard = $guard;
        $this->strategy = $strategy;
    }

    public function isGranted(Tokenable $token, array $attributes, $object = null): bool
    {
        $token = $this->requireAuthentication($token);

        return $this->strategy->decide($token, $attributes ?? [], $object);
    }

    protected function requireAuthentication(Tokenable $token): Tokenable
    {
        if ($this->alwaysAuthenticate || !$token->isAuthenticated()) {
            $token = $this->guard->putAuthenticatedToken($token);
        }

        return $token;
    }

    public function forceAuthentication(bool $force): Grantable
    {
        $this->alwaysAuthenticate = $force;

        return $this;
    }
}