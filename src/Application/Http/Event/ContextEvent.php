<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Event;

use StephBug\SecurityModel\Application\Values\Security\SecurityKey;

class ContextEvent
{
    const SECURITY_PREFIX = 'security_';

    /**
     * @var string
     */
    private $contextKey;

    public function __construct(SecurityKey $securityKey)
    {
        $this->contextKey = $securityKey;
    }

    public function contextKey(): SecurityKey
    {
        return $this->contextKey;
    }

    public function sessionKey(): string
    {
        return self::SECURITY_PREFIX . $this->contextKey->value();
    }
}