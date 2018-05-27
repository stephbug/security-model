<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\InMemory;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\User\Exception\UserNotFound;
use StephBug\SecurityModel\User\UserProvider;
use StephBug\SecurityModel\User\UserSecurity;

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

        if ($user instanceof InMemoryUser) {
            return $user;
        }

        if ($user instanceof Collection && 1 !== count($user)) {
            throw InvalidArgument::reason('In memory user identifier must be unique');
        }

        throw UserNotFound::withEmail($identifier);
    }

    public function refreshUser(UserSecurity $user): UserSecurity
    {
        throw InvalidArgument::reason('In memory user can not be refreshed');
    }

    public function supportsClass(string $class): bool
    {
        return $class === InMemoryUser::class;
    }
}