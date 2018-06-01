<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Firewall\SimplePreAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Http\Response\AuthenticationFailure;
use StephBug\SecurityModel\Application\Http\Response\AuthenticationSuccess;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\SimplePreAuthenticator;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\User\UserProvider;
use StephBugTest\SecurityModel\Mock\SomeSecurityKey;
use StephBugTest\SecurityModel\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SimplePreAuthenticationFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_process_authentication_if_storage_is_not_empty(): void
    {
        $auth = $this->getMockForAbstractClass(SimplePreAuthenticator::class);

        $f = $this->getFirewallInstance($auth);

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);
        $auth->expects($this->never())->method('createToken');

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_authentication_if_authentication_request_does_not_match(): void
    {
        $auth = $this->getMockForAbstractClass(SimplePreAuthenticator::class);

        $f = $this->getFirewallInstance($auth);

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $auth->expects($this->never())->method('createToken');
        $this->authRequest->expects($this->once())->method('matches')->willReturn(false);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_process_pre_authentication_and_return_null_as_response(): void
    {
        $token = $this->token;
        $auth = $this->getAuthenticator($token);

        $f = $this->getFirewallInstance($auth);

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(true);

        $this->events->expects($this->once())->method('attemptLoginEvent');
        $this->manager->expects($this->once())->method('authenticate')->willreturn($token);
        $this->storage->expects($this->once())->method('setToken');

        $this->events->expects($this->once())->method('loginEvent');

        // auth does not implement AuthSuccess
        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_process_pre_authentication_and_return_success_response_from_authenticator(): void
    {
        $token = $this->token;
        $response = new Response('foo');
        $auth = $this->getAuthenticatorWithSuccess($response, $token);

        $f = $this->getFirewallInstance($auth);

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(true);

        $this->events->expects($this->once())->method('attemptLoginEvent');
        $this->manager->expects($this->once())->method('authenticate')->willReturn($token);
        $this->storage->expects($this->once())->method('setToken');

        $this->events->expects($this->once())->method('loginEvent');

        $responseHandled = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals($response, $responseHandled);
    }

    /**
     * @test
     */
    public function it_transform_authentication_exception_to_response(): void
    {
        $token = $this->token;
        $response = new Response('foo');
        $auth = $this->getAuthenticatorWithFailure($response, $token);

        $f = $this->getFirewallInstance($auth);

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(true);

        $this->events->expects($this->once())->method('attemptLoginEvent');

        $exc = new AuthenticationException();
        $this->manager->expects($this->once())->method('authenticate')->willThrowException($exc);

        $this->storage->expects($this->never())->method('setToken');

        $this->events->expects($this->never())->method('loginEvent');
        $this->events->expects($this->once())->method('failureLoginEvent');

        $responseHandled = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals($response, $responseHandled);
    }

    private function getFirewallInstance(SimplePreAuthenticator $authenticator, bool $stateless = false): SimplePreAuthenticationFirewall
    {
        return new SimplePreAuthenticationFirewall(
            $this->guard,
            $authenticator,
            $this->authRequest,
            $this->securityKey,
            $stateless
        );
    }

    private function getAuthenticatorWithSuccess(Response $response, Tokenable $token): SimplePreAuthenticator
    {
        return new class($response, $token) implements SimplePreAuthenticator, AuthenticationSuccess
        {
            private $response;
            private $token;
            public function __construct(Response $response, Tokenable $token)
            {
                $this->response = $response;
                $this->token = $token;
            }

            public function createToken(Request $request, SecurityKey $securityKey): Tokenable
            {
                return $this->token;
            }

            public function onAuthenticationSuccess(Request $request, Tokenable $token): Response
            {
                return $this->response;
            }

            public function authenticateToken(Tokenable $token, UserProvider $userProvider, SecurityKey $securityKey): Tokenable
            {
                // should not be called
            }

            public function supportsToken(Tokenable $token, SecurityKey $securityKey): bool
            {
                // should not be called
            }
        };
    }

    private function getAuthenticatorWithFailure(Response $response, Tokenable $token): SimplePreAuthenticator
    {
        return new class($response, $token) implements SimplePreAuthenticator, AuthenticationFailure
        {
            private $response;
            private $token;
            public function __construct(Response $response, Tokenable $token)
            {
                $this->response = $response;
                $this->token = $token;
            }

            public function createToken(Request $request, SecurityKey $securityKey): Tokenable
            {
                return $this->token;
            }

            public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
            {
                return $this->response;
            }


            public function authenticateToken(Tokenable $token, UserProvider $userProvider, SecurityKey $securityKey): Tokenable
            {
                // should not be called
            }

            public function supportsToken(Tokenable $token, SecurityKey $securityKey): bool
            {
                // should not be called
            }
        };
    }

    private function getAuthenticator(Tokenable $token): SimplePreAuthenticator
    {
        return new class($token) implements SimplePreAuthenticator
        {
            private $token;

            public function __construct($token)
            {
                $this->token = $token;
            }

            public function createToken(Request $request, SecurityKey $securityKey): Tokenable
            {
                return $this->token;
            }

            public function authenticateToken(Tokenable $token, UserProvider $userProvider, SecurityKey $securityKey): Tokenable
            {
                // should not be called
            }

            public function supportsToken(Tokenable $token, SecurityKey $securityKey): bool
            {
                // should not be called
            }
        };
    }

    private function handleFirewall(SimplePreAuthenticationFirewall $firewall, $response)
    {
        return $firewall->handle(new Request(), function () use ($response) {
            return $response;
        });
    }

    private $authRequest;
    private $securityKey;
    private $storage;
    private $manager;
    private $events;
    private $guard;
    private $token;

    public function setUp(): void
    {
        $this->guard = new Guard(
            $this->storage = $this->getMockForAbstractClass(TokenStorage::class),
            $this->manager = $this->getMockForAbstractClass(Authenticatable::class),
            $this->events = $this->getMockForAbstractClass(SecurityEvents::class)
        );

        $this->token = $this->getMockForAbstractClass(Tokenable::class);
        $this->securityKey = new SomeSecurityKey('bar');
        $this->authRequest = $this->getMockForAbstractClass(AuthenticationRequest::class);
    }
}