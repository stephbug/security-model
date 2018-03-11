<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationManager;
use StephBug\SecurityModel\Guard\Authentication\AuthenticationProviders;
use StephBug\SecurityModel\Guard\Authentication\Providers\AuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBugTest\SecurityModel\Unit\TestCase;

class AuthenticationManagerTest extends TestCase
{
    /**
     * @test
     */
    public function it_authenticate_token(): void
    {
        $provider = $this->getMockForAbstractClass(AuthenticationProvider::class);
        $provider->expects($this->once())->method('supports')->willReturn(true);
        $provider->expects($this->once())->method('authenticate')->willReturn($this->token);

        $this->providers->add($provider);
        $auth = $this->getManagerInstance();

        $this->assertEquals($this->token, $auth->authenticate($this->token));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\UnsupportedProvider
     */
    public function it_raise_exception_when_no_authentication_provider_provided(): void
    {
        $auth = $this->getManagerInstance();

        $auth->authenticate($this->token);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\UnsupportedProvider
     */
    public function it_raise_exception_when_no_authentication_provider_supports_token(): void
    {
        $provider = $this->getMockForAbstractClass(AuthenticationProvider::class);
        $provider->expects($this->once())->method('supports')->willReturn(false);
        $provider->expects($this->never())->method('authenticate');

        $this->providers->add($provider);
        $auth = $this->getManagerInstance();

        $auth->authenticate($this->token);
    }

    private function getManagerInstance(): Authenticatable
    {
        return new AuthenticationManager($this->providers);
    }

    private $token;
    private $providers;

    protected function setUp()
    {
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
        $this->providers = new AuthenticationProviders();
    }
}