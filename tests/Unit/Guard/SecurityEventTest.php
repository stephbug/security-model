<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Event\UserAttemptLogin;
use StephBug\SecurityModel\Application\Http\Event\UserFailureLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogin;
use StephBug\SecurityModel\Application\Http\Event\UserLogout;
use StephBug\SecurityModel\Guard\SecurityEvent;
use StephBugTest\SecurityModel\Mock\SomeAuthEvent;
use StephBugTest\SecurityModel\Mock\SomeSecurityKey;
use StephBugTest\SecurityModel\Mock\SomeToken;
use StephBugTest\SecurityModel\Unit\TestCase;

class SecurityEventTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatch_login_event(): void
    {
        $events = new SecurityEvent($this->dispatcher);

        $this->expectedEvent = UserLogin::class;
        $this->dispatcher->listen($this->expectedEvent, [$this, 'onEvent']);

        $events->loginEvent(new Request(), new SomeToken());
        $this->assertNotNull($this->expectedEvent);
    }

    /**
     * @test
     */
    public function it_dispatch_failure_login_event(): void
    {
        $events = new SecurityEvent($this->dispatcher);

        $this->expectedEvent = UserFailureLogin::class;
        $this->dispatcher->listen($this->expectedEvent, [$this, 'onEvent']);

        $events->failureLoginEvent(new SomeSecurityKey('foo'), new Request());
        $this->assertNotNull($this->expectedEvent);
    }

    /**
     * @test
     */
    public function it_dispatch_attempt_login_event(): void
    {
        $events = new SecurityEvent($this->dispatcher);

        $this->expectedEvent = UserAttemptLogin::class;
        $this->dispatcher->listen($this->expectedEvent, [$this, 'onEvent']);

        $events->attemptLoginEvent(new SomeToken(), new Request());
        $this->assertNotNull($this->expectedEvent);
    }

    /**
     * @test
     */
    public function it_dispatch_logout_event(): void
    {
        $events = new SecurityEvent($this->dispatcher);

        $this->expectedEvent = UserLogout::class;
        $this->dispatcher->listen($this->expectedEvent, [$this, 'onEvent']);

        $events->logoutEvent(new SomeToken());

        $this->assertNotNull($this->expectedEvent);
    }

    /**
     * @test
     */
    public function it_dispatch_any_event_as_object(): void
    {
        $events = new SecurityEvent($this->dispatcher);

        $this->expectedEvent = SomeAuthEvent::class;
        $this->dispatcher->listen($this->expectedEvent, [$this, 'onEvent']);

        $result = $events->dispatch(new SomeAuthEvent('foo'));

        $this->assertNotNull($this->expectedEvent);
        $this->assertEquals('foo', $result[0]->value());
    }

    /**
     * @test
     */
    public function it_dispatch_any_event_with_className_and_payload(): void
    {
        $events = new SecurityEvent($this->dispatcher);

        $this->dispatcher->listen(SomeAuthEvent::class, [$this, 'onEvent']);

        $result = $events->dispatch(SomeAuthEvent::class, ['foo']);

        $this->assertEquals('foo', $result[0]);
    }

    /**
     * @test
     */
    public function it_does_not_dispatch_event_on_stateless_request(): void
    {
        $this->markTestSkipped('No expected behaviour with stateless');
        $this->assertNull($this->expectedEvent);

        $events = new SecurityEvent($this->dispatcher);

        $this->expectedEvent = SomeAuthEvent::class;
        $this->dispatcher->listen($this->expectedEvent, [$this, 'onEvent']);

        $events->dispatch(new SomeAuthEvent('foo'), [], $stateless = true);

        $this->assertNull($this->expectedEvent);
    }

    /**
     * @var Dispatcher
     */
    private $dispatcher;
    private $expectedEvent;

    public function setUp()
    {
        $this->dispatcher = new \Illuminate\Events\Dispatcher();
    }

    public function onEvent($event)
    {
        if (is_object($event)) {
            $this->assertEquals($this->expectedEvent, get_class($event));
        }

        return $event;
    }
}