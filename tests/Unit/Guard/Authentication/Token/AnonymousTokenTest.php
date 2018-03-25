<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Identifier\AnonymousIdentifier;
use StephBug\SecurityModel\Application\Values\Security\AnonymousKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\AnonymousToken;
use StephBugTest\SecurityModel\Unit\TestCase;

class AnonymousTokenTest extends TestCase
{

    /**
     * @test
     */
    public function it_set_user_as_anonymous_identifier(): void
    {
        $this->assertInstanceOf(AnonymousIdentifier::class, $this->token->getUser());
    }

    /**
     * @test
     */
    public function it_set_token_as_authenticated(): void
    {
        $this->assertTrue($this->token->isAuthenticated());
    }

    /**
     * @test
     */
    public function it_return_anonymous_key_as_security_key(): void
    {
        $this->assertEquals('foo', $this->token->getSecurityKey()->value());
    }

    /**
     * @test
     */
    public function it_return_empty_credentials(): void
    {
        $this->assertInstanceOf(EmptyCredentials::class, $this->token->getCredentials());
    }

    /**
     * @test
     */
    public function it_has_no_role(): void
    {
        $this->assertEmpty($this->token->getRoles());
    }

    /**
     * @var AnonymousToken
     */
    private $token;

    protected function setUp(): void
    {
        $this->token = new AnonymousToken(new AnonymousIdentifier(), new AnonymousKey('foo'));
    }
}