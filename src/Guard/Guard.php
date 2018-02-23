<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class Guard
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Authenticatable
     */
    private $manager;

    /**
     * @var SecurityEvent
     */
    private $securityEvent;

    public function __construct(TokenStorage $tokenStorage, Authenticatable $manager, SecurityEvent $securityEvent)
    {
        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->securityEvent = $securityEvent;
    }

    public function storage(): TokenStorage
    {
        return $this->tokenStorage;
    }

    public function put(Tokenable $token): void
    {
        $this->tokenStorage->setToken($token);
    }

    public function forget(): void
    {
        $this->tokenStorage->setToken(null);
    }

    public function isStorageEmpty(): bool
    {
        return null === $this->tokenStorage->getToken();
    }

    public function isStorageNotEmpty(): bool
    {
        return !$this->isStorageEmpty();
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        return $this->manager->authenticate($token);
    }

    public function event(): SecurityEvent
    {
        return $this->securityEvent;
    }
}