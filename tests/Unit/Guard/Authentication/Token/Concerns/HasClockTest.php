<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\TokenAttribute;
use StephBug\SecurityModel\Guard\Authentication\Token\Concerns\TokenClock;
use StephBugTest\SecurityModel\Mock\SomeToken;
use StephBugTest\SecurityModel\Unit\TestCase;

class HasClockTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_start_clock(): void
    {
        $t = new SomeToken();

        $this->assertTrue($t->isClocking());
    }

    /**
     * @test
     */
    public function it_can_stop_clock_and_forget_attribute_clock_on_token(): void
    {
        $t = new SomeToken();

        $this->assertTrue($t->isClocking());

        $t->stopClock();

        $this->assertFalse($t->isClocking());
        $this->assertNull($t->getAttribute(TokenAttribute::TOKEN_CLOCK_ATTRIBUTE));
    }

    /**
     * @test
     */
    public function it_can_get_clock_if_token_is_clocking(): void
    {
        $t = new SomeToken();

        $this->assertTrue($t->isClocking());
        $this->assertInstanceOf(TokenClock::class, $t->getClock());
    }

    /**
     * @test
     */
    public function it_return_null_clock_if_token_is_not_clocking(): void
    {
        $t = new SomeToken();

        $t->stopClock();
        $this->assertNull($t->getClock());
    }

    /**
     * @test
     */
    public function it_can_refresh_clock_and_return_clock_instance(): void
    {
        $t = new SomeToken();

        $c1 = $t->getClock();
        $this->assertInstanceOf(TokenClock::class, $c1);

        $c2 = $t->freshClock();
        $this->assertInstanceOf(TokenClock::class, $c1);

        $this->assertNotEquals($c1, $c2);
    }

    /**
     * @test
     */
    public function it_does_not_refresh_clock_and_return_null(): void
    {
        $t = new SomeToken();

        $t->stopClock();

        $this->assertNull($t->freshClock());
    }

    /**
     * @test
     */
    public function it_assert_token_is_clocking(): void
    {
        $t = new SomeToken();
        $this->assertTrue($t->isClocking());

        $t->stopClock();
        $this->assertFalse($t->isClocking());
    }
}