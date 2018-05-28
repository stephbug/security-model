<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Service\Recaller\Encoder;

use StephBug\SecurityModel\Guard\Service\Recaller\Encoder\FreeCookieSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class FreeCookieSecurityTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_same_value_on_encoding(): void
    {
        $encoder = new FreeCookieSecurity();

        $this->assertEquals('foo', $encoder->encode(['foo']));
    }

    /**
     * @test
     */
    public function it_return_same_value_on_decoding(): void
    {
        $encoder = new FreeCookieSecurity();

        $this->assertEquals('foo', $encoder->decode('foo'));
    }

    /**
     * @test
     */
    public function it_always_return_true_on_comparing(): void
    {
        $encoder = new FreeCookieSecurity();

        $this->assertTrue($encoder->compare(['foo'], 'hash'));

        $this->assertTrue($encoder->compare(['foo', 'bar'], 'a_hash'));
    }
}