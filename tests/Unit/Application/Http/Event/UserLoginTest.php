<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Event;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserLogin;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBugTest\SecurityModel\Unit\TestCase;

class UserLoginTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_request(): void
    {
        $event = new UserLogin($request = new Request(), $this->getMockForAbstractClass(Tokenable::class));

        $this->assertEquals($request, $event->request());
    }

    /**
     * @test
     */
    public function it_return_token(): void
    {
        $event = new UserLogin(new Request(), $token = $this->getMockForAbstractClass(Tokenable::class));

        $this->assertEquals($token, $event->token());
    }
}