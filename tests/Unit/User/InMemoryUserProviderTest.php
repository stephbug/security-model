<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\User;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Values\EmailAddress;
use StephBug\SecurityModel\User\InMemoryUser;
use StephBug\SecurityModel\User\InMemoryUserProvider;
use StephBugTest\SecurityModel\Unit\TestCase;

class InMemoryUserProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_assert_it_support_user_class(): void
    {
        $provider = new InMemoryUserProvider($this->users);

        $this->assertTrue($provider->supportsClass(get_class($this->getUser())));

        $this->assertFalse($provider->supportsClass('some_class'));
    }

    /**
     * @test
     */
    public function it_return_user_by_email_identifier(): void
    {
        $this->users->expects($this->once())->method('filter')->willReturn($user = $this->getUser());

        $provider = new InMemoryUserProvider($this->users);

        $this->assertEquals($user, $provider->requireByIdentifier(EmailAddress::fromString('foo@bar.com')));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\User\Exception\UserNotFound
     */
    public function it_raise_exception_when_user_not_found(): void
    {
        $this->users->expects($this->once())->method('filter')->willReturn(null);

        $provider = new InMemoryUserProvider($this->users);

        $provider->requireByIdentifier(EmailAddress::fromString('bar@bar.com'));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_user_found_is_not_unique(): void
    {
        $this->expectExceptionMessage('In memory user identifier must be unique');

        $user = $this->getUser();
        $users = new Collection([$user, $user]);

        $this->users->expects($this->once())->method('filter')->willReturn($users);

        $provider = new InMemoryUserProvider($this->users);

        $provider->requireByIdentifier(EmailAddress::fromString('bar@bar.com'));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\InvalidArgument
     */
    public function it_raise_exception_when_refresh_user(): void
    {
        $provider = new InMemoryUserProvider($this->users);

        $provider->refreshUser($this->getUser());
    }

    private function getUser(): InMemoryUser
    {
        return new InMemoryUser(
            [
                'email' => 'foo@bar.com',
                'roles' => ['ROLE_FOO'],
                'password' => password_hash('password', 1)
            ]
        );
    }

    /**
     * @var \PHPUnit\Framework\MockObject\MockBuilder
     */
    private $users;

    protected function setUp()
    {
        $this->users = $this->getMockBuilder(Collection::class)->getMock();
    }
}