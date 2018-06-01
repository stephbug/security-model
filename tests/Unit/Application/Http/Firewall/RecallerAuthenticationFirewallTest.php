<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Firewall\RecallerAuthenticationFirewall;
use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\SecurityEvents;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\Guard\Service\Recaller\Recallable;
use StephBugTest\SecurityModel\Unit\TestCase;

class RecallerAuthenticationFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_process_recaller_authentication_if_storage_is_not_empty(): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->recallerService->expects($this->never())->method('autoLogin');

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_recaller_authentication_if_recaller_service_does_not_return_a_token(): void
    {
        $f = $this->getFirewallInstance();

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->recallerService->expects($this->once())->method('autoLogin')->willReturn(null);

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_process_recaller_authentication(): void
    {
        $f = $this->getFirewallInstance();

        $token = $this->token;

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->recallerService->expects($this->once())->method('autoLogin')->willReturn($token);

        $this->manager->expects($this->once())->method('authenticate')->willReturn($token);
        $this->storage->expects($this->once())->method('setToken');
        $this->events->expects($this->once())->method('loginEvent');

        $response = $this->handleFirewall($f, 'foo_bar');
        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     */
    public function it_raise_exception_when_manager_fail_to_authenticate_token(): void
    {
        $this->expectExceptionMessage('foo');

        $f = $this->getFirewallInstance();

        $token = $this->token;

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->recallerService->expects($this->once())->method('autoLogin')->willReturn($token);

        $exc = new AuthenticationException('foo');
        $this->manager->expects($this->once())->method('authenticate')->willThrowException($exc);

        $this->handleFirewall($f, 'foo_bar');
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     */
    public function it_make_recaller_service_aware_of_authentication_failure(): void
    {
        $this->expectExceptionMessage('foo');

        $f = $this->getFirewallInstance();

        $token = $this->token;

        $this->storage->expects($this->once())->method('getToken')->willReturn(null);
        $this->recallerService->expects($this->once())->method('autoLogin')->willReturn($token);
        $this->recallerService->expects($this->once())->method('loginFail');

        $exc = new AuthenticationException('foo');
        $this->manager->expects($this->once())->method('authenticate')->willThrowException($exc);

        $this->handleFirewall($f, 'foo_bar');
    }

    private function handleFirewall(RecallerAuthenticationFirewall $firewall, $response)
    {
        return $firewall->handle(new Request(), function () use ($response) {
            return $response;
        });
    }

    private function getFirewallInstance()
    {
        return new RecallerAuthenticationFirewall(
            $this->guard,
            $this->recallerService
        );
    }

    private $storage;
    private $manager;
    private $events;
    private $guard;
    private $recallerService;
    private $token;
    public function setUp(): void
    {
        $this->guard = new Guard(
            $this->storage = $this->getMockForAbstractClass(TokenStorage::class),
            $this->manager = $this->getMockForAbstractClass(Authenticatable::class),
            $this->events = $this->getMockForAbstractClass(SecurityEvents::class)
        );

        $this->recallerService = $this->getMockForAbstractClass(Recallable::class);
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}