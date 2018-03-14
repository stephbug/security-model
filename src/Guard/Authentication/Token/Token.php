<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\HasAttributes;
use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\HasRoles;
use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\HasSerializer;
use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\HasUserChanged;
use StephBug\SecurityModel\User\UserSecurity;

abstract class Token implements Tokenable,Arrayable, Jsonable, JsonSerializable
{
    use HasRoles, HasUserChanged, HasAttributes, HasSerializer;

    /**
     * @var UserToken|SecurityIdentifier|UserSecurity
     */
    private $user;

    /**
     * @var bool
     */
    private $authenticated = false;

    public function setUser(UserToken $user): void
    {
        $this->user = $this->requireSupportedUser($user);
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