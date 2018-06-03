<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Role;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
use StephBug\SecurityModel\Application\Values\Role\RoleValue;
use StephBug\SecurityModel\Role\RoleSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class RoleValueTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $r = new RoleValue('foo');
        $this->assertInstanceOf(RoleValue::class, $r);
        $this->assertInstanceOf(SecurityValue::class,$r);
        $this->assertInstanceOf(RoleSecurity::class, $r);
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $r = new RoleValue('foo');
        $this->assertEquals('foo', $r->getRole());
        $this->assertEquals('foo', (string)$r);
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $r = new RoleValue('foo');
        $r2 = new RoleValue('foo');
        $r3 = new RoleValue('bar');

       $this->assertTrue($r->sameValueAs($r));
       $this->assertTrue($r->sameValueAs($r2));
       $this->assertFalse($r->sameValueAs($r3));
    }
}