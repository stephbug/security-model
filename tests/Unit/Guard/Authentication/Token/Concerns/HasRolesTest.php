<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Role\RoleSecurity;
use StephBugTest\SecurityModel\Mock\SomeToken;
use StephBugTest\SecurityModel\Unit\TestCase;

class HasRolesTest extends TestCase
{
    /**
     * @test
     */
    public function it_construct_any_token_with_roles(): void
    {
        $token = new SomeToken(['foo']);

        $this->assertCount(1, $token->getRoles());
    }

    /**
     * @test
     */
    public function it_transform_string_role(): void
    {
        $token = new SomeToken(['foo']);

        foreach ($token->getRoles() as $role) {
            $this->assertInstanceOf(RoleSecurity::class, $role);
        }
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_role_is_not_a_string_neither_implement_role_security(): void
    {
        new SomeToken([new \stdClass()]);
    }

    /**
     * @test
     */
    public function it_confirm_token_has_roles(): void
    {
        $token = new SomeToken(['foo']);

        $this->assertTrue($token->hasRoles());
    }
}