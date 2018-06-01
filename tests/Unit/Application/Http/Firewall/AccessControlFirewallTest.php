<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Firewall;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\CredentialsNotFound;
use StephBug\SecurityModel\Application\Http\Firewall\AccessControlFirewall;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Contract\Guardable;
use StephBugTest\SecurityModel\Mock\SomeAuthorizationChecker;
use StephBugTest\SecurityModel\Unit\TestCase;

class AccessControlFirewallTest extends TestCase
{
    /**
     * @test
     */
    public function it_grant_access_with_attributes(): void
    {
        $f = $this->getFirewallInstance(['foo']);

        $this->guard->expects($this->once())->method('requireToken')->willReturn($this->token);
        $this->authorizer->expects($this->once())->method('isGranted')->willReturn(true);

        $response = $this->handleFirewall($f);

        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_does_not_process_authorization_without_attribute(): void
    {
        $f = $this->getFirewallInstance([]);

        $this->guard->expects($this->once())->method('requireToken')->willReturn($this->token);
        $this->authorizer->expects($this->never())->method('isGranted');

        $response = $this->handleFirewall($f);

        $this->assertEquals('foo_bar', $response);
    }

    /**
     * @test
     */
    public function it_merge_attributes_to_be_authorized(): void
    {
        $f = new AccessControlFirewall(
            $this->guard,
            $checker = new SomeAuthorizationChecker(true),
            ['foo']
        );

        $this->guard->expects($this->once())->method('requireToken')->willReturn($this->token);

        $response = $this->handleFirewall($f, ['bar']);

        $this->assertEquals('foo_bar', $response);
        $this->assertEquals(['foo', 'bar'], $checker->getAttributes());
    }

    /**
     * @test
     */
    public function it_set_attributes_to_be_authorized(): void
    {
        $f = new AccessControlFirewall(
            $this->guard,
            $checker = new SomeAuthorizationChecker(true)
        );

        $this->guard->expects($this->exactly(2))->method('requireToken')->willReturn($this->token);

        $this->handleFirewall($f, ['bar']);
        $this->assertEquals(['bar'], $checker->getAttributes());

        $f->setAttributes(['baz']);
        $this->handleFirewall($f, ['bar']);
        $this->assertEquals(['baz', 'bar'], $checker->getAttributes());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Role\Exception\AuthorizationDenied
     */
    public function it_raise_exception_when_authorization_id_denied(): void
    {
        $this->expectExceptionMessage('Authorization denied');

        $f = $this->getFirewallInstance(['foo']);

        $this->guard->expects($this->once())->method('requireToken')->willReturn($this->token);
        $this->authorizer->expects($this->once())->method('isGranted')->willReturn(false);

        $this->handleFirewall($f);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\CredentialsNotFound
     */
    public function it_raise_exception_when_token_is_missing(): void
    {
        $f = $this->getFirewallInstance(['foo']);

        $exc = CredentialsNotFound::reason();

        // fixMe need fake guard
        $this->guard->expects($this->once())->method('requireToken')->willThrowException($exc);
        $this->authorizer->expects($this->never())->method('isGranted');

        $this->handleFirewall($f);
    }

    private function handleFirewall(AccessControlFirewall $firewall, array $attributes = [])
    {
        return $firewall->handle(new Request(), function () {
            return 'foo_bar';
        }, $attributes);
    }

    public function getFirewallInstance(array $attributes = []): AccessControlFirewall
    {
        return new AccessControlFirewall(
            $this->guard,
            $this->authorizer,
            $attributes
        );
    }

    private $guard;
    private $authorizer;
    private $token;

    protected function setUp(): void
    {
        $this->guard = $this->getMockForAbstractClass(Guardable::class);
        $this->authorizer = $this->getMockForAbstractClass(Grantable::class);
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}