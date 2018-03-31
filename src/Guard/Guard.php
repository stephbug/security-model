<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard;

use StephBug\SecurityModel\Application\Exception\CredentialsNotFound;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;

class Guard implements Guardable
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Authenticatable
     */
    private $authenticationManager;

    /**
     * @var SecurityEvent
     */
    private $securityEvents;

    public function __construct(TokenStorage $tokenStorage,
                                Authenticatable $authenticationManager,
                                SecurityEvents $securityEvents)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->securityEvents = $securityEvents;
    }

    public function getToken(): ?Tokenable
    {
        return $this->tokenStorage->getToken();
    }

    public function requireToken(): Tokenable
    {
        if (!$token = $this->getToken()) {
            throw CredentialsNotFound::reason();
        }

        return $token;
    }

    public function isStorageEmpty(): bool
    {
        return null === $this->getToken();
    }

    public function isStorageNotEmpty(): bool
    {
        return !$this->isStorageEmpty();
    }

    public function putToken(Tokenable $token): Tokenable
    {
        $this->tokenStorage->setToken($token);

        return $token;
    }

    public function clearStorage(): void
    {
        $this->tokenStorage->setToken(null);
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        return $this->authenticationManager->authenticate($token);
    }

    public function putAuthenticatedToken(Tokenable $token): Tokenable
    {
        return $this->putToken($this->authenticate($token));
    }

    public function dispatch($event, array $payload = [])
    {
        return $this->securityEvents->dispatch($event, $payload);
    }

    public function events(): SecurityEvents
    {
        return $this->securityEvents;
    }
}