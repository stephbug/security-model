<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\User;

use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBugTest\SecurityModel\Unit\TestCase;

class EmptyCredentialsTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_empty_credentials(): void
    {
        $v = new EmptyCredentials();
        $this->assertEmpty($v->credentials());
        $this->assertEquals('',$v->credentials());
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $v = new EmptyCredentials();
        $v2 = new EmptyCredentials();

        $this->assertTrue($v->sameValueAs($v));
        $this->assertTrue($v->sameValueAs($v2));
    }
}