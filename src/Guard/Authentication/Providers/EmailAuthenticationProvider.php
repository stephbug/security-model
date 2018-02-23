<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\UserProvider;
use StephBug\SecurityModel\User\UserSecurity;

class EmailAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        $user = $token->getUser();

        if ($user instanceof UserSecurity) {
            return $token;
        }

        return new EmailToken($this->requireUser($user), new EmptyCredentials());
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof EmailToken;
    }

    private function requireUser(SecurityIdentifier $identifier): UserSecurity
    {
        return $this->userProvider->requireByIdentifier($identifier);
    }
}