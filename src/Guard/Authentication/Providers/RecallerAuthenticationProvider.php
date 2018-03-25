<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\InvalidArgument;
use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\RecallerToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\Exception\BadCredentials;
use StephBug\SecurityModel\User\UserChecker;

class RecallerAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var UserChecker
     */
    private $userChecker;

    /**
     * @var SecurityKey
     */
    private $securityKey;

    /**
     * @var RecallerKey
     */
    private $recallerKey;

    public function __construct(UserChecker $userChecker, SecurityKey $securityKey, RecallerKey $recallerKey)
    {
        if ($securityKey->sameValueAs($recallerKey)) {
            throw InvalidArgument::reason(
                sprintf('Firewall key can not be equals to the recaller key in ', get_class($this))
            );
        }

        $this->userChecker = $userChecker;
        $this->securityKey = $securityKey;
        $this->recallerKey = $recallerKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        $recallerKey = $token->getRecallerKey();

        if (!$recallerKey->sameValueAs($this->recallerKey)) {
            throw BadCredentials::invalid($recallerKey);
        }

        $this->userChecker->onPreAuthentication($user = $token->getUser());

        return new RecallerToken($user, $this->securityKey, $this->recallerKey);
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof RecallerToken && $token->getSecurityKey()->sameValueAs($this->securityKey);
    }
}