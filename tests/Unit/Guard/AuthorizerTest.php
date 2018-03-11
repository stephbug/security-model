<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard;

use StephBug\SecurityModel\Application\Exception\CredentialsNotFound;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Grantable;
use StephBug\SecurityModel\Guard\Authorizer;
use StephBug\SecurityModel\Guard\Guard;
use StephBugTest\SecurityModel\Unit\TestCase;

class AuthorizerTest extends TestCase
{
    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\CredentialsNotFound
     */
    public function it_raise_exception_if_token_storage_is_empty(): void
    {
        $exception = new CredentialsNotFound('not found');
        $this->guard->expects($this->once())->method('requireToken')->will($this->throwException($exception));

        $auth = $this->authorizerInstance();

        $auth->grant(['foo']);
    }

    /**
     * @test
     */
    public function it_grant_access(): void
    {
        $auth = $this->authorizerInstance();
        $token = $this->getMockForAbstractClass(Tokenable::class);

        $this->guard->expects($this->once())->method('requireToken')->willReturn($token);
        $this->grantable->expects($this->once())->method('isGranted')->willReturn(true);

        $this->assertTrue($auth->grant(['foo'], new \stdClass()));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Role\Exception\AuthorizationDenied
     */
    public function it_raise_exception_when_access_is_denied(): void
    {
        $auth = $this->authorizerInstance();
        $token = $this->getMockForAbstractClass(Tokenable::class);

        $this->guard->expects($this->once())->method('requireToken')->willReturn($token);
        $this->grantable->expects($this->once())->method('isGranted')->willReturn(false);

        $auth->requireGranted(['foo'], new \stdClass());
    }

    private function authorizerInstance(): Authorizer
    {
        return new Authorizer($this->guard, $this->grantable);
    }

    private $guard;
    private $grantable;

    protected function setUp()
    {
        $this->guard = $this->getMockBuilder(Guard::class)->disableOriginalConstructor()->getMock();
        $this->grantable = $this->getMockForAbstractClass(Grantable::class);
    }
}