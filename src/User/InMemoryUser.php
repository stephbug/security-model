<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress as EmailContract;
use StephBug\SecurityModel\Application\Values\Contract\EncodedPassword;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;
use StephBug\SecurityModel\Application\Values\Role\RoleValue;
use StephBug\SecurityModel\Application\Values\User\BcryptPassword;
use StephBug\SecurityModel\Application\Values\User\EmailAddress;
use StephBug\SecurityModel\Application\Values\User\InMemoryUserId;
use StephBug\SecurityModel\Role\RoleSecurity;

class InMemoryUser implements LocalUser
{
    /**
     * @var array
     */
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getIdentifier(): SecurityIdentifier
    {
        return $this->getEmail();
    }

    public function getId(): UniqueIdentifier
    {
        return InMemoryUserId::nextIdentity();
    }

    public function getEmail(): EmailContract
    {
        return EmailAddress::fromString($this->attributes['email']);
    }

    public function getRoles(): Collection
    {
        $roles = $this->attributes['roles'];

        if ($roles instanceof Collection) {
            $roles = $this->transformRoles($roles);
        }

        if (is_array($roles)) {
            $roles = $this->transformRoles(new Collection($roles));
        }

        return $this->attributes['roles'] = $roles;
    }

    public function getPassword(): EncodedPassword
    {
        return new BcryptPassword($this->attributes['password']);
    }

    public function eraseCredentials(): void
    {
    }

    private function transformRoles(Collection $roles): Collection
    {
        if ($roles->isEmpty()) {
            throw InvalidArgument::reason('In memory user roles can not be empty');
        }

        return $roles->transform(function ($role) {
            if ($role instanceof RoleSecurity) {
                return $role;
            } elseif (is_string($role)) {
                return new RoleValue($role);
            }

            throw InvalidArgument::reason(
                sprintf('In memory user roles must be a string or implement %s contract', RoleSecurity::class)
            );
        });
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}