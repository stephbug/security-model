<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed;
use StephBug\SecurityModel\Application\Http\Entrypoint\Entrypoint;
use StephBug\SecurityModel\Application\Http\Firewall\HttpBasicAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\AuthenticationRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\EncodedPassword;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\User\LocalUser;
use StephBugTest\SecurityModel\Mock\SomeEmailIdentifier;
use StephBugTest\SecurityModel\Mock\SomeIdentifier;
use StephBugTest\SecurityModel\Mock\SomeSecurityKey;
use StephBugTest\SecurityModel\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicAuthenticationFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_process_authentication_with_null_identifier(): void
    {
        $f = $this->getFirewallInstance();

        $this->authRequest->expects($this->once())->method('extract')->willReturn([null]);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_authentication_with_invalid_identifier(): void
    {
        $f = $this->getFirewallInstance();

        $exc = new SecurityValueFailed('foo', 401, '', '');
        $this->authRequest->expects($this->once())->method('extract')->willThrowException($exc);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_authentication_with_wrong_type_identifier_and_return_entrypoint_response(): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once())->method('getToken');

        $exc = new SecurityValueFailed('foo', 401, '', '');

        // wrong type id make the authentication process required
        // it would fail and raise exception during the creation of token
        $wrongId = [new SomeIdentifier('foo')];
        $this->authRequest->expects($this->at(0))->method('extract')->willReturn($wrongId);
        $this->authRequest->expects($this->at(1))->method('extract')->willThrowException($exc);

        $this->storage->expects($this->once())->method('setToken'); //clear storage
        $this->events->expects($this->once())->method('failureLoginEvent');

        $response = new Response('foo');
        $this->entrypoint->expects($this->once())->method('startAuthentication')->willReturn($response);

        $responseHandled = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals($response, $responseHandled);
    }

    /**
     * @test
     * @dataProvider provideInvalidCredentials
     */
    public function it_does_not_process_authentication_with_invalid_credentials_and_return_entrypoint_response($credentials): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once())->method('getToken');

        $wrongId = [new SomeIdentifier('foo')];
        $this->authRequest->expects($this->at(0))->method('extract')->willReturn($wrongId);
        $this->authRequest->expects($this->at(1))->method('extract')->willReturn($credentials);

        $this->storage->expects($this->once())->method('setToken'); //clear storage
        $this->events->expects($this->once())->method('failureLoginEvent');

        $response = new Response('foo');
        $this->entrypoint->expects($this->once())->method('startAuthentication')->willReturn($response);

        $responseHandled = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals($response, $responseHandled);
    }

    /**
     * @test
     */
    public function it_does_not_process_authentication_when_user_is_already_authenticated(): void
    {
        $f = $this->getFirewallInstance();

        $id = new SomeEmailIdentifier('foo@bar.com');
        $token = new IdentifierPasswordToken(
            $this->getUser($id),
            new EmptyCredentials(),
            $this->securityKey,
            ['ROLE_FOO']
        );

        $this->storage->expects($this->once())->method('getToken')->willReturn($token);
        $this->authRequest->expects($this->once())->method('extract')->willReturn([$id]);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_process_authentication(): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once(1))->method('getToken');

        // fixMe as we tied to an email, the wrong id should not happen
        // we need to be explicit on teh authentication request
        $wrongId = [new SomeIdentifier('foo')];
        $this->authRequest->expects($this->at(0))->method('extract')->willReturn($wrongId);

        $credentials = [new SomeEmailIdentifier('foo@bar.com'), new EmptyCredentials()];
        $this->authRequest->expects($this->at(1))->method('extract')->willReturn($credentials);

        $this->events->expects($this->once())->method('attemptLoginEvent');

        $token = $this->getMockForAbstractClass(Tokenable::class);
        $this->manager->expects($this->once())->method('authenticate')->willReturn($token);
        $this->storage->expects($this->once(1))->method('setToken');

        $this->events->expects($this->once())->method('loginEvent');

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    public function provideInvalidCredentials(): array
    {
        // checkMe this results is tied to his Http Basic Authentication request
        return [[null, null], ['', null], [null, '']];
    }

    private function getUser(EmailIdentifier $identifier): LocalUser
    {
        return new class($identifier) implements LocalUser, UserToken
        {
            private $identifier;

            public function __construct(EmailIdentifier $identifier)
            {
                $this->identifier = $identifier;
            }

            public function getPassword(): EncodedPassword
            {
            }

            public function eraseCredentials(): void
            {
            }

            public function getIdentifier(): SecurityIdentifier
            {
            }

            public function getId(): UniqueIdentifier
            {
            }

            public function getEmail(): EmailIdentifier
            {
                return $this->identifier;
            }

            public function getRoles(): Collection
            {
            }
        };
    }

    private function handleFirewall(HttpBasicAuthenticationFirewall $firewall, $response)
    {
        return $firewall->handle(new Request(), function () use ($response) {
            return $response;
        });
    }

    private function getFirewallInstance(): HttpBasicAuthenticationFirewall
    {
        return new HttpBasicAuthenticationFirewall(
            $this->guard,
            $this->authRequest,
            $this->entrypoint,
            $this->securityKey
        );
    }

    private $authRequest;
    private $securityKey;
    private $storage;
    private $manager;
    private $events;
    private $guard;
    private $entrypoint;

    public function setUp(): void
    {
        $this->guard = new Guard(
            $this->storage = $this->getMockForAbstractClass(TokenStorage::class),
            $this->manager = $this->getMockForAbstractClass(Authenticatable::class),
            $this->events = $this->getMockForAbstractClass(SecurityEvents::class)
        );

        $this->securityKey = new SomeSecurityKey('bar');
        $this->authRequest = $this->getMockForAbstractClass(AuthenticationRequest::class);
        $this->entrypoint = $this->getMockForAbstractClass(Entrypoint::class);
    }
}