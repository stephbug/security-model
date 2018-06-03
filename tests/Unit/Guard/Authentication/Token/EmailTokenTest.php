<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token;

use Ramsey\Uuid\Uuid;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
use StephBugTest\SecurityModel\Mock\UserSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class EmailTokenTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_a_user(): void
    {
        $this->assertEquals($this->userToken, $this->tokenInstance()->getUser());
    }

    /**
     * @test
     */
    public function it_return_a_security_key(): void
    {
        $this->assertEquals($this->securityKey, $this->tokenInstance()->getSecurityKey());
    }

    /**
     * @test
     */
    public function it_return_empty_credentials(): void
    {
        $this->assertEquals(new EmptyCredentials(), $this->tokenInstance()->getCredentials());
    }

    /**
     * @test
     */
    public function it_set_token_as_authenticated_with_roles(): void
    {
        $this->assertFalse($this->tokenInstance()->isAuthenticated());
        $this->assertTrue($this->tokenInstance(['foo'])->isAuthenticated());
    }

    private function tokenInstance(array $roles = [])
    {
        return new EmailToken($this->userToken, $this->securityKey, $roles);
    }

    private $securityKey;
    private $userToken;

    protected function setUp()
    {
        $this->securityKey = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()->getMock();
        $this->userToken = new UserSecurity(Uuid::uuid4(), true);
    }
}