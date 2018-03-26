<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Providers;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Role\SwitchUserRole;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBug\SecurityModel\Application\Values\User\EmptyCredentials;
use StephBug\SecurityModel\Guard\Authentication\Providers\EmailAuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\User\UserChecker;
use StephBug\SecurityModel\User\UserProvider;
use StephBugTest\SecurityModel\Mock\UserSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class EmailAuthenticationProviderTest extends TestCase
{
    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\UnsupportedProvider
     */
    public function it_raise_exception_when_token_is_not_supported(): void
    {
        $this->securityKey->expects($this->once())->method('sameValueAs')->willReturn(false);

        $token = new EmailToken(
            new UserSecurity(Uuid::uuid4(), false),
            new EmptyCredentials(),
            $this->securityKey
        );

        $this->providerInstance()->authenticate($token);
    }

    /**
     * @test
     */
    public function it_return_same_instance_of_user_if_it_implement_user_security_contract(): void
    {
        $this->securityKey->expects($this->once())->method('sameValueAs')->willReturn(true);
        $this->expectsOnUserChecker();

        $token = new EmailToken(
            $user = $this->getUserSecurityInstance(),
            new EmptyCredentials(),
            $this->securityKey
        );

        $authenticatedToken = $this->providerInstance()->authenticate($token);

        $this->assertNotSame($authenticatedToken, $token);
        $this->assertEquals($user, $authenticatedToken->getUser());
    }

    /**
     * @test
     */
    public function it_retrieve_user_through_user_provider(): void
    {
        $this->securityKey->expects($this->once())->method('sameValueAs')->willReturn(true);
        $this->expectsOnUserChecker();

        $token = new EmailToken(
            EmailIdentifier::fromString('foo@bar.com'), new EmptyCredentials(), $this->securityKey
        );

        $user = $this->getUserSecurityInstance();
        $this->userProvider->expects($this->once())->method('requireByIdentifier')->willReturn($user);

        $authenticatedToken = $this->providerInstance()->authenticate($token);

        $this->assertNotEquals($authenticatedToken, $token);
        $this->assertEquals($user, $authenticatedToken->getUser());
    }

    /**
     * @test
     */
    public function it_add_switch_user_role_to_authenticated_token(): void
    {
        $this->securityKey->expects($this->once())->method('sameValueAs')->willReturn(true);
        $this->expectsOnUserChecker();

        $role = new SwitchUserRole('foo', $this->getMockForAbstractClass(Tokenable::class));
        $token = new EmailToken(
            $this->getUserSecurityInstance(),
            new EmptyCredentials(),
            $this->securityKey,
            [$role]
        );

        $this->assertCount(1, $token->getRoles());
        $this->assertCount(0, $token->getUser()->getRoles());

        $authenticatedToken = $this->providerInstance()->authenticate($token);

        $this->assertNotSame($authenticatedToken, $token);
        $this->assertContains($role, $authenticatedToken->getRoles());
    }

    private function providerInstance(): EmailAuthenticationProvider
    {
        return new EmailAuthenticationProvider($this->userProvider, $this->userChecker, $this->securityKey);
    }

    private function getUserSecurityInstance(): UserSecurity
    {
        return new UserSecurity(Uuid::uuid4(), false);
    }

    private function expectsOnUserChecker(): void
    {
        $this->userChecker->expects($this->once())->method('onPreAuthentication');
        $this->userChecker->expects($this->never())->method('onPostAuthentication');
    }

    /**
     * @var MockObject|UserProvider
     */
    private $userProvider;

    /**
     * @var MockObject|UserChecker
     */
    private $userChecker;

    /**
     * @var MockObject|SecurityKey
     */
    private $securityKey;

    protected function setUp()
    {
        $this->userProvider = $this->getMockForAbstractClass(UserProvider::class);
        $this->userChecker = $this->getMockForAbstractClass(UserChecker::class);
        $this->securityKey = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()->getMock();
    }
}