<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Security;

use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Application\Values\Security\SecurityKey;
use StephBugTest\SecurityModel\Unit\TestCase;

class RecallerKeyTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $key = new RecallerKey('foo');

        $this->assertInstanceOf(SecurityKey::class, $key);
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $key = new RecallerKey('foo');
        $key2 = new RecallerKey('bar');

        $this->assertTrue($key->sameValueAs($key));
        $this->assertFalse($key->sameValueAs($key2));
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $key = new RecallerKey('foo');

        $this->assertEquals('foo', $key->value());
        $this->assertEquals('foo', (string)$key);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     * @dataProvider provideInvalidKeyValue
     */
    public function it_raise_exception_when_key_is_invalid($value = null): void
    {
        new RecallerKey($value);
    }

    public function provideInvalidKeyValue()
    {
        return [[], [null], [''], [new \stdClass()]];
    }
}