<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Exception\UnsupportedUser;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\User\Exception\UserNotFound;

class InMemoryUserProvider implements UserProvider
{
    /**
     * @var Collection
     */
    private $inMemoryUsers;

    public function __construct(Collection $inMemoryUsers)
    {
        $this->inMemoryUsers = $inMemoryUsers;
    }

    public function requireByIdentifier(SecurityIdentifier $identifier): UserSecurity
    {
        if (!$identifier instanceof EmailAddress) {
            throw InvalidArgument::reason('In memory user only supports email as an identifier');
        }

        $user = $this->inMemoryUsers->filter(function (InMemoryUser $user) use ($identifier) {
            return $user->getIdentifier()->sameValueAs($identifier);
        });

        if ($user->isEmpty()) {
            throw UserNotFound::withEmail($identifier);
        }

        if (1 !== $user->count()) {
            throw InvalidArgument::reason('In memory user identifier must be unique');
        }

        return $user->first();
    }

    public function refreshUser(UserSecurity $user): UserSecurity
    {
        if (!$this->supportsClass(get_class($user))) {
            throw UnsupportedUser::withUser($user);
        }

        return $this->requireByIdentifier($user->getIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === InMemoryUser::class;
    }
}