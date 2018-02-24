<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use Illuminate\Contracts\Hashing\Hasher;
use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\Exception\BadCredentials;
use StephBug\SecurityModel\User\Exception\UserNotFound;
use StephBug\SecurityModel\User\LocalUser;
use StephBug\SecurityModel\User\UserChecker;
use StephBug\SecurityModel\User\UserProvider;

class IdentifierPasswordAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @var UserChecker
     */
    private $userChecker;

    /**
     * @var Hasher
     */
    private $encoder;

    public function __construct(UserProvider $userProvider, UserChecker $userChecker, Hasher $encoder)
    {
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->encoder = $encoder;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        $user = $this->retrieveUser($token);

        try {
            $this->checkUser($user, $token);

            return $this->createAuthenticatedToken($user, $token);
        } catch (BadCredentials $badCredentials) {
            throw UserNotFound::hideBadCredentials($badCredentials);
        }
    }

    private function retrieveUser(IdentifierPasswordToken $token): LocalUser
    {
        if ($token->getUser() instanceof LocalUser) {
            return $token->getUser();
        }

        return $this->userProvider->requireByIdentifier($token->getIdentifier());
    }

    private function checkUser(LocalUser $user, IdentifierPasswordToken $token): void
    {
        $this->userChecker->onPreAuthentication($user);

        $this->checkCredentials($user, $token);

        $this->userChecker->onPostAuthentication($user);
    }

    private function checkCredentials(LocalUser $user, IdentifierPasswordToken $token): void
    {
        $currentUser = $token->getUser();

        if ($currentUser instanceof LocalUser) {
            if (!$currentUser->getPassword()->sameValueAs($user->getPassword())) {
                throw BadCredentials::hasChanged();
            }
        } else {
            $presentedPassword = $token->getCredentials();

            if ($presentedPassword instanceof EmptyCredentials) {
                throw BadCredentials::invalid($presentedPassword);
            }

            if (!$this->encoder->check($presentedPassword, $user->getPassword()->credentials())) {
                throw BadCredentials::invalid();
            }
        }
    }

    private function createAuthenticatedToken(LocalUser $user, IdentifierPasswordToken $token): IdentifierPasswordToken
    {
        return new IdentifierPasswordToken(
            $user,
            $token->getCredentials(),
            $this->getRoles($user, $token)
        );
    }

    private function getRoles(LocalUser $user, IdentifierPasswordToken $token): array
    {
        $roles = $user->getRoles()->all();

        foreach ($token->getRoles() as $role) {
            // todo switch user role
        }

        return $roles;
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof IdentifierPasswordToken;
    }
}