<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserImpersonated;
use StephBug\SecurityModel\User\UserSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class UserImpersonatedTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_a_user(): void
    {
        $event = new UserImpersonated(
            $user = $this->getMockForAbstractClass(UserSecurity::class),
            new Request()
        );

        $this->assertEquals($user, $event->target());
    }

    /**
     * @test
     */
    public function it_return_request(): void
    {
        $event = new UserImpersonated(
            $this->getMockForAbstractClass(UserSecurity::class),
            $request = new Request()
        );

        $this->assertEquals($request, $event->request());
    }
}