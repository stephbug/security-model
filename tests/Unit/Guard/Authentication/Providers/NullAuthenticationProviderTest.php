<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Providers;

use StephBug\SecurityModel\Guard\Authentication\Providers\NullAuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBugTest\SecurityModel\Unit\TestCase;

class NullAuthenticationProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_support_any_token(): void
    {
        $provider = new NullAuthenticationProvider();

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $this->assertTrue($provider->supports($token));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\UnsupportedProvider
     */
    public function it_raise_exception_on_authentication_any_time(): void
    {
        $provider = new NullAuthenticationProvider();

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $provider->authenticate($token);
    }
}