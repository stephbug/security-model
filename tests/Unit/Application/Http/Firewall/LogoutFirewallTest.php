<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Firewall\LogoutFirewall;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Response\LogoutSuccess;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBug\SecurityModel\Guard\Service\Logout\Logout;
use StephBugTest\SecurityModel\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class LogoutFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_logout_service(): void
    {
        //fixMe add handler own test, logout event dispatched

        $f = $this->getFirewallWithAuthenticationRequired();

        $response = new Response('foo');
        $this->response->expects($this->once())->method('onLogoutSuccess')->willReturn($response);

        $this->guard->expects($this->once())->method('requireToken')->willReturn($this->token);
        $this->guard->expects($this->once())->method('clearStorage');
        // fixMe mock securityEvents and test logout event dispatched separately
        $this->guard->expects($this->once())->method('events');

        $ref = new \ReflectionClass($f);
        $h = $ref->getProperty('logoutHandlers');
        $h->setAccessible(true);
        $this->assertEmpty($h->getValue($f));

        $handler = $this->getLogoutHandler($response, $this->token);
        $f->addHandler($handler);

        $this->assertCount(1,$h->getValue($f));
        $_handlers = $h->getValue($f);
        $this->assertEquals($handler, $_handlers[0]);

        $responseHandled = $this->handleFirewall($f, 'foo_bar');

        $this->assertEquals($response, $responseHandled);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_no_logout_handler_has_been_added(): void
    {
        $f = $this->getFirewallWithAuthenticationRequired();

        $this->expectExceptionMessage('No logout handler has been added to class', get_class($f));

        $this->response->expects($this->never())->method('onLogoutSuccess');
        $this->guard->expects($this->never())->method('requireToken');
        $this->guard->expects($this->never())->method('clearStorage');
        $this->guard->expects($this->never())->method('events');

        $ref = new \ReflectionClass($f);
        $h = $ref->getProperty('logoutHandlers');
        $h->setAccessible(true);
        $this->assertEmpty($h->getValue($f));

        $this->handleFirewall($f, 'foo_bar');
    }

    /**
     * @test
     */
    public function it_does_not_process_logout_if_storage_is_empty(): void
    {
        $f = $this->getFirewallInstance();

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(true);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_logout_if_token_is_anonymous(): void
    {
        $f = $this->getFirewallInstance();

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(false);
        $this->trustResolver->expects($this->once())->method('isAnonymous')->willReturn(true);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_logout_if_auth_request_does_not_match(): void
    {
        $f = $this->getFirewallInstance();

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(false);
        $this->trustResolver->expects($this->once())->method('isAnonymous')->willReturn(false);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(false);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    private function getFirewallWithAuthenticationRequired(): LogoutFirewall
    {
        $f = $this->getFirewallInstance();

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(false);
        $this->trustResolver->expects($this->once())->method('isAnonymous')->willReturn(false);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(true);

        return $f;
    }

    private function getLogoutHandler(Response $response, Tokenable $token): Logout
    {
        return new class($response, $token, $this) implements Logout
        {
            private $response;
            private $token;
            private $unit;

            public function __construct(Response $response, Tokenable $token, TestCase $unit)
            {
                $this->response = $response;
                $this->token = $token;
                $this->unit = $unit;
            }

            public function logout(Request $request, Response $response, Tokenable $token): void
            {
                $this->unit->assertEquals($this->response, $response);
                $this->unit->assertEquals($this->token, $token);
            }
        };
    }

    private function handleFirewall(LogoutFirewall $firewall, $response)
    {
        return $firewall->handle(new Request(), function () use ($response) {
            return $response;
        });
    }

    private function getFirewallInstance(): LogoutFirewall
    {
        return new LogoutFirewall(
            $this->guard,
            $this->authRequest,
            $this->trustResolver,
            $this->response
        );
    }

    private $guard;
    private $token;
    private $response;
    private $trustResolver;
    private $authRequest;

    public function setUp(): void
    {
        $this->guard = $this->getMockForAbstractClass(Guardable::class);
        $this->trustResolver = $this->getMockForAbstractClass(TrustResolver::class);
        $this->response = $this->getMockForAbstractClass(LogoutSuccess::class);
        $this->authRequest = $this->getMockForAbstractClass(AuthenticationRequest::class);
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}