<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Firewall\SwitchUserAuthenticationFirewall;
use StephBug\SecurityModel\Application\Http\Request\SwitchUserMatcher;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\User\UserProvider;
use StephBugTest\SecurityModel\Mock\SomeEmailIdentifier;
use StephBugTest\SecurityModel\Mock\SomeSecurityKey;
use StephBugTest\SecurityModel\Unit\TestCase;

class SwitchUserAuthenticationFirewallTest extends TestCase
{
    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\CredentialsNotFound
     */
    public function it_raise_exception_if_token_is_missing(): void
    {
        $f = $this->getFirewallInstance();

        $this->handleFirewall($f, new Request(), ['foo_bar']);
    }

    /**
     * @test
     */
    public function it_determine_if_authentication_is_required_with_authentication_request(): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(false);

        $response = $this->handleFirewall($f, new Request(), 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_determine_if_authentication_is_required_with_roles_attributes(): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->authRequest->expects($this->once())->method('matches')->willReturn(true);
        $this->authorizer->expects($this->at(0))->method('isGranted')->willReturn(false);
        $this->authorizer->expects($this->at(1))->method('isGranted')->willReturn(false);

        $response = $this->handleFirewall($f, new Request(), 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_attempt_impersonate_user(): void
    {
        $f = $this->getFirewallInstance();

        // required
        $this->storage->expects($this->exactly(2))->method('getToken')->willReturn($this->token);

        $this->authRequest->expects($this->once())->method('matches')->willReturn(true);
        $this->authorizer->expects($this->at(0))->method('isGranted')->willReturn(true);
        $this->authorizer->expects($this->at(1))->method('isGranted')->willReturn(true);

        //id
        $id = new SomeEmailIdentifier('foo@bar.com');

        $this->authRequest->expects($this->once())->method('extract')->willReturn($id);

        // need user mock on token
    }

    private function handleFirewall(SwitchUserAuthenticationFirewall $firewall, Request $request, $response)
    {
        return $firewall->handle($request, function () use ($response) {
            return $response;
        });
    }

    private function getFirewallInstance(bool $stateless = false): SwitchUserAuthenticationFirewall
    {
        return new SwitchUserAuthenticationFirewall(
            $this->guard,
            $this->authorizer,
            $this->authRequest,
            $this->userProvider,
            $this->securityKey,
            $stateless
        );
    }

    private $authRequest;
    private $securityKey;
    private $storage;
    private $manager;
    private $events;
    private $guard;
    private $authorizer;
    private $userProvider;
    private $token;


    public function setUp(): void
    {
        $this->guard = new Guard(
            $this->storage = $this->getMockForAbstractClass(TokenStorage::class),
            $this->manager = $this->getMockForAbstractClass(Authenticatable::class),
            $this->events = $this->getMockForAbstractClass(SecurityEvents::class)
        );

        $this->securityKey = new SomeSecurityKey('bar');
        $this->authRequest = $this->getMockBuilder(SwitchUserMatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->authorizer = $this->getMockForAbstractClass(Grantable::class);
        $this->userProvider = $this->getMockForAbstractClass(UserProvider::class);
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}