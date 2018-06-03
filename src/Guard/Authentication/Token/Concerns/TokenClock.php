<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

class TokenClock
{
    const DEFAULT_EXPIRATION_INTERVAL = 'PT24H';

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var \DateInterval
     */
    private $interval;

    public function __construct()
    {
        $this->createdAt = $this->fromNow();
    }

    public function isExpired(): bool
    {
        if (!$this->interval instanceof \DateInterval) {
            $this->interval = new \DateInterval(static::DEFAULT_EXPIRATION_INTERVAL);
        }

        return $this->createdAt->add($this->interval) < $this->fromNow();
    }

    public function fromNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setInterval(\DateInterval $interval): TokenClock
    {
        $this->interval = $interval;

        return $this;
    }

    public function getInterval(): ?\DateInterval
    {
        return $this->interval;
    }
}