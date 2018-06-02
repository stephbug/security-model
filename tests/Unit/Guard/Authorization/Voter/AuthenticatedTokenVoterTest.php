<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBug\SecurityModel\Guard\Authorization\Voter\AuthenticatedTokenVoter;
use StephBugTest\SecurityModel\Unit\TestCase;

class AuthenticatedTokenVoterTest extends TestCase
{
    /**
     * @test
     */
    public function it_authenticate_token_on_fully_attribute(): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->trustResolver->expects($this->once())->method('isFullyAuthenticated')->willReturn(true);

        $result = $v->vote($this->token, [$v->fully()]);
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     */
    public function it_authenticate_token_on_remember_attribute(): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->trustResolver->expects($this->any())->method('isFullyAuthenticated')->willReturn(false);
        $this->trustResolver->expects($this->once())->method('isRememberMe')->willReturn(true);

        $result = $v->vote($this->token, [$v->remembered()]);
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     */
    public function it_authenticate_token_on_anonymous_attribute(): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->trustResolver->expects($this->never())->method('isFullyAuthenticated');
        $this->trustResolver->expects($this->never())->method('isRememberMe');
        $this->trustResolver->expects($this->once())->method('isAnonymous')->willReturn(true);

        $result = $v->vote($this->token, [$v->anonymously()]);
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     * @dataProvider provideSupportedAttributes
     */
    public function it_deny_vote_when_token_is_not_supported($attributes): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->trustResolver->expects($this->any())->method('isFullyAuthenticated')->willReturn(false);
        $this->trustResolver->expects($this->any())->method('isRememberMe')->willReturn(false);
        $this->trustResolver->expects($this->any())->method('isAnonymous')->willReturn(false);

        $result = $v->vote($this->token, $attributes);
        $this->assertEquals(-1, $result);
    }

    /**
     * @test
     * @dataProvider provideUnsupportedAttributes
     */
    public function it_does_not_vote_on_unsupported_attributes($attributes): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->trustResolver->expects($this->never())->method('isFullyAuthenticated');
        $this->trustResolver->expects($this->never())->method('isRememberMe');
        $this->trustResolver->expects($this->never())->method('isAnonymous')->willReturn(true);

        $result = $v->vote($this->token, $attributes);
        $this->assertEquals(0, $result);
    }

    /**
     * @test
     */
    public function it_access_authenticated_fully_attribute(): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->assertEquals(AuthenticatedTokenVoter::FULLY, $v->fully());
    }

    /**
     * @test
     */
    public function it_access_authenticated_remember_attribute(): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->assertEquals(AuthenticatedTokenVoter::REMEMBERED, $v->remembered());
    }

    /**
     * @test
     */
    public function it_access_authenticated_anonymous_attribute(): void
    {
        $v = new AuthenticatedTokenVoter($this->trustResolver);

        $this->assertEquals(AuthenticatedTokenVoter::ANONYMOUSLY, $v->anonymously());
    }

    public function provideUnsupportedAttributes(): array
    {
        return [
            [['foo', '', null]]
        ];
    }

    public function provideSupportedAttributes()
    {
        return [
            [
                [AuthenticatedTokenVoter::FULLY],
                [AuthenticatedTokenVoter::REMEMBERED],
                [AuthenticatedTokenVoter::ANONYMOUSLY]
            ]
        ];
    }

    private $trustResolver;
    private $token;

    public function setUp(): void
    {
        $this->trustResolver = $this->getMockForAbstractClass(TrustResolver::class);
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }

}