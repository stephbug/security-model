<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use Illuminate\Contracts\Hashing\Hasher;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\Exception\BadCredentials;
use StephBug\SecurityModel\User\LocalUser;
use StephBug\SecurityModel\User\UserChecker;
use StephBug\SecurityModel\User\UserProvider;
use StephBug\SecurityModel\User\UserSecurity;

class IdentifierPasswordAuthenticationProvider extends UserAuthenticationProvider
{
    /**
     * @var Hasher
     */
    private $encoder;

    public function __construct(UserProvider $userProvider,
                                UserChecker $userChecker,
                                SecurityKey $securityKey,
                                Hasher $encoder)
    {
        parent::__construct($userProvider, $userChecker, $securityKey);

        $this->encoder = $encoder;
    }

    protected function retrieveUser(Tokenable $token): UserSecurity
    {
        if ($token->getUser() instanceof LocalUser) {
            return $token->getUser();
        }

        return $this->userProvider->requireByIdentifier($token->getIdentifier());
    }

    protected function checkUser(UserSecurity $user, Tokenable $token): void
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

    protected function createAuthenticatedToken(UserSecurity $user, Tokenable $token): Tokenable
    {
        return new IdentifierPasswordToken(
            $user,
            $token->getCredentials(),
            $this->securityKey,
            $this->getRoles($user, $token)
        );
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof IdentifierPasswordToken && $this->securityKey->sameValueAs($token->getSecurityKey());
    }
}