<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\User\UserSecurity;

interface Tokenable
{
    public function getRoles(): Collection;

    public function setUser(UserToken $user): void;

    /**
     * @return UserToken|SecurityIdentifier|UserSecurity
     */
    public function getUser(): UserToken;

    public function getIdentifier(): SecurityIdentifier;

    public function setAuthenticated(bool $authenticated);

    public function isAuthenticated(): bool;

    public function getCredentials(): Credentials;

    public function getSecurityKey(): SecurityKey;

    public function setAttribute(string $attribute, $value): void;

    public function getAttribute(string $attribute, $default = null);

    public function hasAttribute(string $attribute): bool;

    public function forgetAttribute(string $attribute): bool;

    public function getAttributes(): iterable;
}