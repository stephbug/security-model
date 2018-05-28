<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Service\Logout;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Service\Logout\SessionLogout;
use StephBugTest\SecurityModel\Mock\SomeToken;
use StephBugTest\SecurityModel\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SessionLogoutTest extends TestCase
{
    /**
     * @test
     */
    public function it_flush_session(): void
    {
        $request = new Request();
        $session = $this->getMockForAbstractClass(Session::class);
        $session->expects($this->once())->method('flush');

        $request->setLaravelSession($session);

        $logout = new SessionLogout();

        $logout->logout($request, new Response('bar'), new SomeToken());
    }
}