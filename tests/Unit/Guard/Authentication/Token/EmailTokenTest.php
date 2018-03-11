<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
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
    public function it_return_credentials(): void
    {
        $this->assertEquals($this->credentials, $this->tokenInstance()->getCredentials());
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
        return new EmailToken($this->userToken, $this->credentials, $this->securityKey, $roles);
    }

    private $securityKey;
    private $credentials;
    private $userToken;

    protected function setUp()
    {
        $this->credentials = $this->getMockForAbstractClass(Credentials::class);
        $this->securityKey = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()->getMock();
        $this->userToken = $this->getMockForAbstractClass(UserToken::class);
    }
}