<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\Exception\UserNotFound;
use StephBug\SecurityModel\User\UserProvider;
use StephBug\SecurityModel\User\UserSecurity;

class UserProviders
{
    /**
     * @var array
     */
    private $userProviders;

    public function __construct(array $userProviders = null)
    {
        $this->userProviders = new Collection($userProviders ?? []);
    }

    public function add(UserProvider $userProvider): self
    {
        $this->userProviders->push($userProvider);

        return $this;
    }

    public function refreshUser(Tokenable $token): ?Tokenable
    {
        $user = $token->getUser();
        if (!$user instanceof UserSecurity) {
            return $token;
        }

        try {
            $user = $this->firstSupportedProvider($user)->refreshUser($user);
            $token->setUser($user);

            return $token;
        } catch (UserNotFound $userNotFound) {
            return null;
        }
    }

    protected function firstSupportedProvider(UserSecurity $user): UserProvider
    {
        return $this->userProviders
            ->push(new NullUserProvider())
            ->first(function (UserProvider $provider) use ($user) {
                return $provider->supportsClass(get_class($user));
            });
    }
}