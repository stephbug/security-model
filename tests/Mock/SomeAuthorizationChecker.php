<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Grantable;

class SomeAuthorizationChecker implements Grantable
{
    private $token;
    private $attributes;
    private $object;

    /**
     * @var bool
     */
    private $grant;

    /**
     * @var bool
     */
    private $force;

    public function __construct(bool $grant, bool $force = false)
    {
        $this->grant = $grant;
        $this->force = $force;
    }

    public function isGranted(Tokenable $token, array $attributes, $object = null): bool
    {
        $this->token = $token;
        $this->attributes = $attributes;
        $this->object = $object;

        return $this->grant;
    }

    public function forceAuthentication(bool $force): Grantable
    {
        return $this; //fixMe
    }

    public function getToken(): Tokenable
    {
        return $this->token;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }
}