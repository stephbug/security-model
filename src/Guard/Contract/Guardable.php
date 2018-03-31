<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Contract;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

interface Guardable
{
    public function getToken(): ?Tokenable;

    public function requireToken(): Tokenable;

    public function isStorageEmpty(): bool;

    public function isStorageNotEmpty(): bool;

    public function putToken(Tokenable $token): void;

    public function clearStorage(): void;

    public function authenticate(Tokenable $token): Tokenable;

    public function putAuthenticatedToken(Tokenable $token): Tokenable;

    public function events(): SecurityEvents;

    public function dispatch($event, array $payload = []);
}