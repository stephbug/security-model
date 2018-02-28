<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use StephBug\SecurityModel\Application\Values\FirewallKey;

class ContextEvent
{
    const SECURITY_PREFIX = 'security_';

    /**
     * @var string
     */
    private $contextKey;

    public function __construct(FirewallKey $firewallKey)
    {
        $this->contextKey = $firewallKey;
    }

    public function contextKey(): FirewallKey
    {
        return $this->contextKey;
    }

    public function sessionKey(): string
    {
        return self::SECURITY_PREFIX . $this->contextKey->value();
    }
}