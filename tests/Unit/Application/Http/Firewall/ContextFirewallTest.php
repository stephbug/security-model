<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBug\SecurityModel\Application\Http\Firewall\ContextFirewall;
use StephBug\SecurityModel\Application\Values\Providers\UserProviders;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBugTest\SecurityModel\Mock\SomeExtendedToken;
use StephBugTest\SecurityModel\Mock\SomeIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class ContextFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatch_context_event(): void
    {
        $f = $this->getFirewallInstance();

        $this->guard->expects($this->once())->method('dispatch'); //fixMe guard fake

        $request = Request::create('/', 'GET', []);
        $session = $this->getMockForAbstractClass(Session::class);
        $request->setLaravelSession($session);

        $session->expects($this->once())->method('get')->willReturn(null);

        $response = $this->handleFirewall($f, $request, 'foo_bar');

        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_refresh_user_from_serialize_token(): void
    {
        $f = $this->getFirewallInstance();

        $token = new SomeExtendedToken(new SomeIdentifier('baz'));

        $this->guard->expects($this->once())->method('dispatch');
        $this->guard->expects($this->once())->method('clearStorage');
        $this->userProviders->expects($this->once())->method('refreshUser')->willReturn($token);
        $this->guard->expects($this->once())->method('putToken');

        $request = Request::create('/', 'GET', []);
        $session = $this->getMockForAbstractClass(Session::class);
        $request->setLaravelSession($session);

        $tokenString = $this->getSerializedToken($token);
        $session->expects($this->once())->method('get')->willReturn($tokenString);

        $response = $this->handleFirewall($f, $request, 'foo_bar');

        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_refresh_user_if_unserialize_token_is_not_an_instance_of_tokenable(): void
    {
        $f = $this->getFirewallInstance();

        $this->guard->expects($this->once())->method('dispatch');
        $this->guard->expects($this->once())->method('clearStorage');
        $this->userProviders->expects($this->never())->method('refreshUser');

        $request = Request::create('/', 'GET', []);
        $session = $this->getMockForAbstractClass(Session::class);
        $request->setLaravelSession($session);

        $session->expects($this->once())->method('get')->willReturn('s:7:"bar bar";');

        $response = $this->handleFirewall($f, $request, 'foo_bar');

        $this->assertEquals('foo_bar', $response);
    }

    private function handleFirewall(ContextFirewall $firewall, Request $request, $response)
    {
        return $firewall->handle($request, function () use ($response) {
            return $response;
        });
    }

    private function getFirewallInstance(): ContextFirewall
    {
        return new ContextFirewall(
            $this->guard,
            $this->userProviders,
            $this->event
        );
    }

    private function getSerializedToken(SomeExtendedToken $token): string
    {
        return serialize($token);
    }

    private $guard;
    private $userProviders;
    private $event;
    private $token;

    public function setUp(): void
    {
        $this->guard = $this->getMockForAbstractClass(Guardable::class);
        $this->userProviders = $this->getMockBuilder(UserProviders::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->event = $this->getMockBuilder(ContextEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}