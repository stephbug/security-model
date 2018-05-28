<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

interface Tokenable extends \Serializable
{
    public function getRoles(): array;

    public function setUser(UserToken $user): void;

    public function getUser(): UserToken;

    public function getIdentifier(): SecurityIdentifier;

    public function setAuthenticated(bool $authenticated);

    public function isAuthenticated(): bool;

    public function getCredentials(): Credentials;

    public function getSecurityKey(): SecurityKey;

    public function eraseCredentials(): void;

    public function setAttribute(string $attribute, $value): void;

    public function getAttribute(string $attribute, $default = null);

    public function hasAttribute(string $attribute): bool;

    public function forgetAttribute(string $attribute): bool;

    public function getAttributes(): iterable;
}