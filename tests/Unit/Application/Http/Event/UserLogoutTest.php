<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Event;

use StephBug\SecurityModel\Application\Http\Event\UserLogout;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBugTest\SecurityModel\Unit\TestCase;

class UserLogoutTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_token(): void
    {
        $event = new UserLogout($token = $this->getMockForAbstractClass(Tokenable::class));

        $this->assertEquals($token, $event->token());
    }
}