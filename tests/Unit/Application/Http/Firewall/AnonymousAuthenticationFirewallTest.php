<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Firewall\AnonymousAuthenticationFirewall;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBugTest\SecurityModel\Unit\TestCase;

class AnonymousAuthenticationFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_process_authentication(): void
    {
        $f = $this->getFirewallInstance('foo');

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(false);

        $response = $this->handleFirewall($f, 'foo_bar');

        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_process_authentication(): void
    {
        $f = $this->getFirewallInstance('foo');

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(true);
        $this->guard->expects($this->once())->method('putAuthenticatedToken')->willReturn($this->token);

        $response = $this->handleFirewall($f, 'foo_bar');

        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_raise_exception_when_authentication_failed(): void
    {
        $f = $this->getFirewallInstance('foo');

        $this->guard->expects($this->once())->method('isStorageEmpty')->willReturn(true);

        $exc = new AuthenticationException();
        $this->guard->expects($this->once())->method('putAuthenticatedToken')->willThrowException($exc);

        $response = $this->handleFirewall($f, 'foo_bar');

        $this->assertEquals('foo_bar', $response);
    }

    private function handleFirewall(AnonymousAuthenticationFirewall $firewall, $response)
    {
        return $firewall->handle(new Request(), function () use ($response) {
            return $response;
        });
    }

    private function getFirewallInstance(string $key): AnonymousAuthenticationFirewall
    {
        return new AnonymousAuthenticationFirewall(
            $this->guard,
            new AnonymousKey($key)
        );
    }

    private $guard;
    private $token;

    public function setUp(): void
    {
        $this->guard = $this->getMockForAbstractClass(Guardable::class);
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}