<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

trait HasClock
{
    /**
     * @var bool
     */
    protected $clock = true;

    public function freshClock(): ?TokenClock
    {
        if ($this->clock) {
            $this->setAttribute(
                TokenAttribute::TOKEN_CLOCK_ATTRIBUTE,
                $clock = new TokenClock()
            );

            return $clock;
        }

        return null;
    }

    public function getClock(): ?TokenClock
    {
        if ($this->clock) {
            return $this->getAttribute(TokenAttribute::TOKEN_CLOCK_ATTRIBUTE);
        }

        return null;
    }

    public function stopClock(): void
    {
        if ($this->isClocking()) {
            $this->clock = false;

            $this->forgetAttribute(TokenAttribute::TOKEN_CLOCK_ATTRIBUTE);
        }
    }

    public function isClocking(): bool
    {
        return $this->clock;
    }
}