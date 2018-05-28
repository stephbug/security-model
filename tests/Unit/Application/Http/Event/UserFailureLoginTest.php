<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserFailureLogin;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBugTest\SecurityModel\Mock\SomeSecurityKey;
use StephBugTest\SecurityModel\Unit\TestCase;

class UserFailureLoginTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_security_key(): void
    {
        $event = new UserFailureLogin(
            $key = new SomeSecurityKey('bar'),
            new Request()
        );

        $this->assertEquals($key, $event->securityKey());
    }

    /**
     * @test
     */
    public function it_return_request(): void
    {
        $event = new UserFailureLogin(
            new SomeSecurityKey('foo'),
            $request = new Request()
        );

        $this->assertEquals($request, $event->request());
    }
}