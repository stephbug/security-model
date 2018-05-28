<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\User;

use StephBug\SecurityModel\Role\RoleSecurity;
use StephBug\SecurityModel\User\InMemory\InMemoryUser;
use StephBugTest\SecurityModel\Unit\TestCase;

class InMemoryUserTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed_with_array_of_attributes(): void
    {
        $user = new InMemoryUser($attributes = $this->getAttributes());

        $this->assertSame($user->getAttributes(), $attributes);
    }

    /**
     * @test
     */
    public function it_transform_roles(): void
    {
        $user = new InMemoryUser($attributes = $this->getAttributes());

        $user->getRoles()->each(function($role){
            $this->assertInstanceOf(RoleSecurity::class, $role);
        });
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_if_role_is_empty(): void
    {
        $this->expectExceptionMessage('In memory user roles can not be empty');

        $attributes = [
            'email' => 'foo@bar.com',
            'roles' => [],
            'password' => password_hash('password', 1)
        ];

        $user = new InMemoryUser($attributes);

        $user->getRoles();
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_if_role_is_wrong_type(): void
    {
        $this->expectExceptionMessage(
            sprintf('In memory user roles must be a string or implement %s contract', RoleSecurity::class)
        );

        $attributes = [
            'email' => 'foo@bar.com',
            'roles' => [new \stdClass()],
            'password' => password_hash('password', 1)
        ];

        $user = new InMemoryUser($attributes);

        $user->getRoles();
    }

    private function getAttributes(): array
    {
        return [
            'email' => 'foo@bar.com',
            'roles' => ['ROLE_FOO'],
            'password' => password_hash('password', 1)
        ];
    }
}