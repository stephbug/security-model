<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\User\UserSecurity;

abstract class Token implements Tokenable
{
    /**
     * @var Collection
     */
    private $roles;

    /**
     * @var UserToken|SecurityIdentifier|UserSecurity
     */
    private $user;

    /**
     * @var bool
     */
    private $authenticated = false;

    public function __construct(array $roles = [])
    {
        $this->roles = new Collection($roles ?? []);
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setUser(UserToken $user): void
    {
        $this->user = $user;
    }

    public function getUser(): UserToken
    {
        return $this->user;
    }

    public function getIdentifier(): SecurityIdentifier
    {
        if ($this->user instanceof UserSecurity) {
            return $this->user->getIdentifier();
        }

        return $this->user;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function setAuthenticated(bool $authenticated): void
    {
        $this->authenticated = $authenticated;
    }
}