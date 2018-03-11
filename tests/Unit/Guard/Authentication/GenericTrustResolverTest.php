<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication;

use StephBug\SecurityModel\Guard\Authentication\GenericTrustResolver;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBug\SecurityModel\Guard\Authentication\Token\IdentifierPasswordToken;
use StephBug\SecurityModel\Guard\Authentication\Token\RecallerToken;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBugTest\SecurityModel\Unit\TestCase;

class GenericTrustResolverTest extends TestCase
{
    /**
     * @test
     */
    public function it_assert_token_is_anonymous(): void
    {
        $trust = $this->getTrustInstance();

        $this->assertFalse($trust->isAnonymous());
        $this->assertFalse($trust->isAnonymous($this->getRecallerMock()));
        $this->assertFalse($trust->isAnonymous($this->getAuthenticatedTokenMock()));
        $this->assertTrue($trust->isAnonymous($this->getAnonymousMock()));
    }

    /**
     * @test
     */
    public function it_assert_token_is_remember_me(): void
    {
        $trust = $this->getTrustInstance();

        $this->assertFalse($trust->isRememberMe());
        $this->assertFalse($trust->isRememberMe($this->getAnonymousMock()));
        $this->assertFalse($trust->isRememberMe($this->getAuthenticatedTokenMock()));
        $this->assertTrue($trust->isRememberMe($this->getRecallerMock()));
    }

    /**
     * @test
     */
    public function it_assert_token_is_fully_authenticated(): void
    {
        $trust = $this->getTrustInstance();

        $this->assertFalse($trust->isFullyAuthenticated());
        $this->assertFalse($trust->isFullyAuthenticated($this->getAnonymousMock()));
        $this->assertFalse($trust->isFullyAuthenticated($this->getRecallerMock()));
        $this->assertTrue($trust->isFullyAuthenticated($this->getAuthenticatedTokenMock()));
    }

    private function getTrustInstance(): TrustResolver
    {
        return new GenericTrustResolver(AnonymousToken::class, RecallerToken::class);
    }

    private function getAnonymousMock()
    {
        return $this->getMockBuilder(AnonymousToken::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getRecallerMock()
    {
        return $this->getMockBuilder(RecallerToken::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getAuthenticatedTokenMock()
    {
        return $this->getMockBuilder(IdentifierPasswordToken::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}