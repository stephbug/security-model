<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\UserSecurity;

class EmailAuthenticationProvider extends UserAuthenticationProvider
{
    protected function retrieveUser(Tokenable $token): UserSecurity
    {
        $user = $token->getUser();

        if ($user instanceof UserSecurity) {
            return $user;
        }

        return $this->userProvider->requireByIdentifier($token->getIdentifier());
    }

    protected function checkUser(UserSecurity $user, Tokenable $token): void
    {
        $this->userChecker->onPreAuthentication($user);
    }

    protected function createAuthenticatedToken(UserSecurity $user, Tokenable $token): Tokenable
    {
        return new EmailToken($user, new EmptyCredentials(), $this->securityKey, $this->getRoles($user, $token));
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof EmailToken && $this->securityKey->sameValueAs($token->getSecurityKey());
    }
}