<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Event;

use StephBug\SecurityModel\Application\Http\Event\ContextEvent;
use StephBugTest\SecurityModel\Mock\SomeSecurityKey;
use StephBugTest\SecurityModel\Unit\TestCase;

class ContextEventTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_security_Key(): void
    {
        $event = new ContextEvent($key = new SomeSecurityKey('foo'));

        $this->assertEquals($key, $event->contextKey());
    }

    /**
     * @test
     */
    public function it_return_session_key(): void
    {
        $event = new ContextEvent($key = new SomeSecurityKey('foo'));

        $this->assertEquals(ContextEvent::SECURITY_PREFIX . 'foo', $event->sessionKey());
    }
}