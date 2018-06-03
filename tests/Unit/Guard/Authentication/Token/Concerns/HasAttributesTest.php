<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authentication\Token\Concerns;

use StephBugTest\SecurityModel\Mock\SomeToken;
use StephBugTest\SecurityModel\Unit\TestCase;

class HasAttributesTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_attribute(): void
    {
        $t = new SomeToken();

        $t->setAttribute('foo','bar');
        $this->assertEquals('bar', $t->getAttribute('foo'));
    }

    /**
     * @test
     */
    public function it_can_get_attribute(): void
    {
        $t = new SomeToken();

        $t->setAttribute('foo','bar');
        $this->assertEquals('bar', $t->getAttribute('foo'));
    }

    /**
     * @test
     */
    public function it_assert_attribute_exist(): void
    {
        $t = new SomeToken();
        $this->assertFalse($t->hasAttribute('foo'));
        $t->setAttribute('foo','bar');
        $this->assertTrue($t->hasAttribute('foo'));
    }

    /**
     * @test
     */
    public function it_can_successfully_remove_attribute(): void
    {
        $t = new SomeToken();

        $t->setAttribute('foo','bar');
        $this->assertTrue($t->hasAttribute('foo'));

        $result = $t->forgetAttribute('foo');

        $this->assertFalse($t->hasAttribute('foo'));
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_fail_remove_attribute(): void
    {
        $t = new SomeToken();

        $this->assertFalse($t->hasAttribute('foo'));

        $result = $t->forgetAttribute('foo');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_acces_all_attributes(): void
    {
        $t = new SomeToken();
        $t->stopClock();

        $this->assertEmpty($t->getAttributes());

        $t->setAttribute('foo','bar');
        $t->setAttribute('baz','foo_bar');

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'foo_bar'],
            $t->getAttributes()
        );
    }
}