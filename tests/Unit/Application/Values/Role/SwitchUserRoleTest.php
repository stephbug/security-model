<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Role;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Role\RoleValue;
use StephBug\SecurityModel\Application\Values\Role\SwitchUserRole;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Role\RoleSecurity;
use StephBugTest\SecurityModel\Mock\SomeIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class SwitchUserRoleTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $r = new SwitchUserRole('foo', $this->token);
        $this->assertInstanceOf(RoleValue::class, $r);
        $this->assertInstanceOf(SecurityValue::class,$r);
        $this->assertInstanceOf(RoleSecurity::class, $r);
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $r = new SwitchUserRole('foo', $this->token);
        $this->assertEquals('foo', $r->getRole());
        $this->assertEquals('foo', (string)$r);
    }

    /**
     * @test
     */
    public function it_access_token(): void
    {
        $r = new SwitchUserRole('foo', $this->token);

        $this->assertEquals($this->token, $r->source());
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $r = new SwitchUserRole('foo', $this->token);
        $r2 = new SwitchUserRole('foo', $this->token);

        $this->token->expects($this->exactly(2))->method('getIdentifier')->willReturn(new SomeIdentifier('bar_bar'));

        $this->assertTrue($r->sameValueAs($r2));
    }

    /**
     * @test
     */
    public function it_can_be_compared_2(): void
    {
        $r = new SwitchUserRole('foo', $this->token);
        $r2 = new SwitchUserRole('foo', $this->token);

        $this->token->expects($this->at(0))->method('getIdentifier')->willReturn(new SomeIdentifier('bar_bar'));
        $this->token->expects($this->at(1))->method('getIdentifier')->willReturn(new SomeIdentifier('baz_baz'));

        $this->assertFalse($r->sameValueAs($r2));
    }

    private $token;
    public function setUp(): void
    {
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}