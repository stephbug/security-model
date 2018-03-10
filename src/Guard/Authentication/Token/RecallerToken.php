<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Application\Values\FirewallKey;
use StephBug\SecurityModel\Application\Values\RecallerKey;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\User\UserSecurity;

class RecallerToken extends Token
{
    /**
     * @var FirewallKey
     */
    private $firewallKey;

    /**
     * @var RecallerKey
     */
    private $recallerKey;

    public function __construct(UserSecurity $user, FirewallKey $firewallKey, RecallerKey $recallerKey)
    {
        parent::__construct();

        $this->setUser($user);
        $this->firewallKey = $firewallKey;
        $this->recallerKey = $recallerKey;

        $this->setAuthenticated(true);
    }

    public function getCredentials(): Credentials
    {
        return new EmptyCredentials();
    }

    public function getSecurityKey(): SecurityKey
    {
        return $this->firewallKey;
    }

    public function getRecallerKey(): RecallerKey
    {
        return $this->recallerKey;
    }
}