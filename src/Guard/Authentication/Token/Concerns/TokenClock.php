<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

class TokenClock
{
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

    public function isClockExpired(): bool
    {
        $interval = $this->interval ?? new \DateInterval('PT24H');

        return $this->createdAt->add($interval) < $this->fromNow();
    }

    public function setClockInterval(\DateInterval $interval): TokenClock
    {
        $this->interval = $interval;

        return $this;
    }

    protected function fromNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}