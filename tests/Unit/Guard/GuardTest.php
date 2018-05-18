<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard;

use StephBug\SecurityModel\Guard\Authentication\Authenticatable;
use StephBug\SecurityModel\Guard\Authentication\Token\Storage\TokenStorage;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Guard;
use StephBug\SecurityModel\Guard\SecurityEvent;
use StephBugTest\SecurityModel\Unit\TestCase;

class GuardTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_a_security_event_instance(): void
    {
        $guard = $this->guardInstance();

        $this->assertInstanceOf(SecurityEvent::class, $guard->events());
    }

    /**
     * @test
     */
    public function it_authenticate_a_token(): void
    {
        $guard = $this->guardInstance();
        $this->manager->expects($this->once())->method('authenticate')->willReturn($this->token);

        $this->assertEquals($this->token, $guard->authenticate($this->token));
    }

    /**
     * @test
     */
    public function it_put_token_on_storage(): void
    {
        $guard = $this->guardInstance();

        $this->assertNull($this->storage->getToken());
        $this->storage->expects($this->once())->method('setToken');

        $guard->putToken($this->token);
    }

    /**
     * @test
     */
    public function it_authenticate_and_put_token_on_storage(): void
    {
        $guard = $this->guardInstance();

        $this->assertNull($this->storage->getToken());
        $this->storage->expects($this->once())->method('setToken');

        $this->manager->expects($this->once())->method('authenticate')->willReturn($this->token);

        $this->assertEquals($this->token, $guard->putAuthenticatedToken($this->token));
    }

    /**
     * @test
     */
    public function it_clear_token_on_storage(): void
    {
        $guard = $this->guardInstance();

        $this->assertNull($this->storage->getToken());
        $this->storage->expects($this->once())->method('setToken');
        $guard->clearStorage();
    }

    /**
     * @test
     */
    public function it_check_if_storage_is_empty(): void
    {
        $guard = $this->guardInstance();

        $this->storage->expects($this->exactly(2))->method('getToken')->willReturn(null);

        $this->assertTrue($guard->isStorageEmpty());
        $this->assertFalse($guard->isStorageNotEmpty());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\CredentialsNotFound
     */
    public function it_raise_exception_when_token_storage_is_empty(): void
    {
        $guard = $this->guardInstance();
        $this->storage->expects($this->once())->method('getToken')->willReturn(null);

        $guard->requireToken();
    }

    private function guardInstance(): Guard
    {
        return new Guard($this->storage, $this->manager, $this->events);
    }

    private $storage;
    private $manager;
    private $events;
    private $token;

    protected function setUp()
    {
        $this->storage = $this->getMockBuilder(TokenStorage::class)->getMock();
        $this->assertNull($this->storage->getToken());

        $this->manager = $this->getMockBuilder(Authenticatable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->events = $this->getMockBuilder(SecurityEvent::class)
            ->disableOriginalConstructor()->getMock();

        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}