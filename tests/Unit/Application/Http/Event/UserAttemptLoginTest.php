<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserAttemptLogin;
use StephBugTest\SecurityModel\Mock\SomeToken;
use StephBugTest\SecurityModel\Unit\TestCase;

class UserAttemptLoginTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_token(): void
    {
        $event = new UserAttemptLogin(
            $token = new SomeToken(),
            new Request()
        );

        $this->assertEquals($token, $event->token());
    }

    /**
     * @test
     */
    public function it_return_request(): void
    {
        $event = new UserAttemptLogin(
            new SomeToken(),
            $request = new Request()
        );

        $this->assertEquals($request, $event->request());
    }
}