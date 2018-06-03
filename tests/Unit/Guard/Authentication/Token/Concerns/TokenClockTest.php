<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\TokenClock;
use StephBugTest\SecurityModel\Unit\TestCase;

class TokenClockTest extends TestCase
{
    /**
     * @test
     */
    public function it_access_creation_date_from_self_constructed(): void
    {
        $c = new TokenClock();

        $this->assertInstanceOf(\DateTimeInterface::class, $c->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $c->getCreatedAt());
    }

    /**
     * @test
     */
    public function it_can_generate_datetime_with_utc_timezone_from_now(): void
    {
        $c = new TokenClock();

        $this->assertInstanceOf(\DateTimeInterface::class, $c->fromNow());
        $this->assertInstanceOf(\DateTimeImmutable::class, $c->fromNow());
        $this->assertInstanceOf(\DateTimeZone::class, $c->fromNow()->getTimezone());
    }

    /**
     * @test
     */
    public function it_can_check_if_clock_is_expired_with_self_interval(): void
    {
        $c = new TokenClock();

        $this->assertFalse($c->isExpired());
    }

    /**
     * @test
     */
    public function it_can_check_if_clock_is_expired_with_interval_set(): void
    {
        $c = new TokenClock();

        $this->assertFalse($c->isExpired());

        $c->setInterval($int = new \DateInterval('PT1S'));

        $this->assertEquals($int, $c->getInterval());

        sleep(2);

        $this->assertTrue($c->isExpired());
    }
}