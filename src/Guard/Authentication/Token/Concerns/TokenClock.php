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

    public function isExpired(): bool
    {
        $interval = $this->interval ?? new \DateInterval('PT24H');

        return $this->createdAt->add($interval) < $this->fromNow();
    }

    public function setInterval(\DateInterval $interval): TokenClock
    {
        $this->interval = $interval;

        return $this;
    }

    public function fromNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getInterval(): ?\DateInterval
    {
        return $this->interval;
    }

}