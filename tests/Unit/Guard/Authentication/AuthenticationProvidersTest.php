<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\AuthenticationProviders;
use StephBug\SecurityModel\Guard\Authentication\Providers\AuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Providers\NullAuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBugTest\SecurityModel\Unit\TestCase;

class AuthenticationProvidersTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed_with_empty_providers(): void
    {
        $providers = new AuthenticationProviders();

        $this->assertInstanceOf(AuthenticationProviders::class, $providers);
    }

    /**
     * @test
     */
    public function it_can_add_authentication_provider(): void
    {
        $providers = new AuthenticationProviders();

        $provider = $this->getMockForAbstractClass(AuthenticationProvider::class);
        $provider->expects($this->once())->method('supports')->willReturn(true);

        $providers->add($provider);

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $this->assertEquals($provider, $providers->firstSupportedProvider($token));
    }

    /**
     * @test
     */
    public function it_return_a_null_authentication_provider_when_no_authentication_provider_provided(): void
    {
        $providers = new AuthenticationProviders();

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $this->assertInstanceOf(NullAuthenticationProvider::class, $providers->firstSupportedProvider($token));
    }
}