<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Providers;

use Illuminate\Contracts\Hashing\Hasher;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use StephBug\SecurityModel\Application\Values\EmptyCredentials;
use StephBug\SecurityModel\Application\Values\SecurityKey;
use StephBug\SecurityModel\Guard\Authentication\Providers\IdentifierPasswordAuthenticationProvider;
use StephBug\SecurityModel\Guard\Authentication\Token\EmailToken;
use StephBug\SecurityModel\User\UserChecker;
use StephBug\SecurityModel\User\UserProvider;
use StephBugTest\SecurityModel\Mock\LocalUserSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class IdentifierPasswordAuthenticationProviderTest extends TestCase
{
    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\UnsupportedProvider
     */
    public function it_raise_exception_when_token_is_not_supported(): void
    {
        $this->securityKey->expects($this->never())->method('sameValueAs');

        $token = new EmailToken(
            $this->getLocalUser(),
            new EmptyCredentials(),
            $this->securityKey
        );

        $this->providerInstance()->authenticate($token);
    }

    private function providerInstance(): IdentifierPasswordAuthenticationProvider
    {
        return new IdentifierPasswordAuthenticationProvider(
            $this->userProvider, $this->userChecker, $this->securityKey, $this->encoder
        );
    }

    private function getLocalUser(): LocalUserSecurity
    {
        return new LocalUserSecurity(Uuid::uuid4(), false);
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

    /**
     * @var MockObject|Hasher
     */
    private $encoder;

    protected function setUp()
    {
        $this->userProvider = $this->getMockForAbstractClass(UserProvider::class);
        $this->userChecker = $this->getMockForAbstractClass(UserChecker::class);
        $this->securityKey = $this->getMockBuilder(SecurityKey::class)
            ->disableOriginalConstructor()->getMock();
        $this->encoder = $this->getMockForAbstractClass(Hasher::class);
    }
}