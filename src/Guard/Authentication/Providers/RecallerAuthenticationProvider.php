<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Providers;

use StephBug\SecurityModel\Application\Exception\UnsupportedProvider;
use StephBug\SecurityModel\Application\Values\FirewallKey;
use StephBug\SecurityModel\Application\Values\RecallerKey;
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
     * @var FirewallKey
     */
    private $firewallKey;

    /**
     * @var RecallerKey
     */
    private $recallerKey;

    public function __construct(UserChecker $userChecker, FirewallKey $firewallKey, RecallerKey $recallerKey)
    {
        $this->userChecker = $userChecker;
        $this->firewallKey = $firewallKey;
        $this->recallerKey = $recallerKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        if (!$this->supports($token)) {
            throw UnsupportedProvider::withSupport($token, $this);
        }

        if ($token->getRecalleKey()->sameValueAs($this->recallerKey)) {
            throw BadCredentials::invalid($token->getRecallerKey());
        }

        $this->userChecker->onPreAuthentication($user = $token->getUser());

        return new RecallerToken($user, $this->firewallKey, $this->recallerKey);
    }

    public function supports(Tokenable $token): bool
    {
        return $token instanceof RecallerToken && $token->getSecurityKey()->sameValueAs($this->firewallKey);
    }
}