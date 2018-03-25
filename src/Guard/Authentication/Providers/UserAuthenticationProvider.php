<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\Role\SwitchUserRole;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\Exception\BadCredentials;
use StephBug\SecurityModel\User\Exception\UserNotFound;
use StephBug\SecurityModel\User\UserChecker;
use StephBug\SecurityModel\User\UserProvider;
use StephBug\SecurityModel\User\UserSecurity;

abstract class UserAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var UserChecker
     */
    protected $userChecker;

    /**
     * @var SecurityKey
     */
    protected $securityKey;

    public function __construct(UserProvider $userProvider, UserChecker $userChecker, SecurityKey $securityKey)
    {
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->securityKey = $securityKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        $user = $this->retrieveUser($token);

        try {
            $this->checkUser($user, $token);
        } catch (BadCredentials $badCredentials) {
            throw UserNotFound::hideBadCredentials($badCredentials);
        }

        return $this->createAuthenticatedToken($user, $token);
    }

    abstract protected function retrieveUser(Tokenable $token): UserSecurity;

    abstract protected function checkUser(UserSecurity $user, Tokenable $token): void;

    abstract protected function createAuthenticatedToken(UserSecurity $user, Tokenable $token): Tokenable;

    protected function getRoles(UserSecurity $user, Tokenable $token): array
    {
        $roles = $user->getRoles()->all();

        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchUserRole) {
                $roles[] = $role;
            }
        }

        return $roles;
    }
}