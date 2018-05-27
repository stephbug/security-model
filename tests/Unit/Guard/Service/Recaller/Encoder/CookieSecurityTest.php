<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Service\Recaller\Encoder;

use StephBug\SecurityModel\Application\Values\Security\RecallerKey;
use StephBug\SecurityModel\Guard\Service\Recaller\Encoder\CookieSecurity;
use StephBugTest\SecurityModel\Unit\TestCase;

class CookieSecurityTest extends TestCase
{
    /**
     * @test
     */
    public function it_encode_cookie(): void
    {
        $key = new RecallerKey('foo');
        $encoder = new CookieSecurity($key);
        $values = ['foobar', 'bar'];

        $hash = $encoder->encode($values);

        $this->assertTrue($this->isBase64($hash));
    }

    /**
     * @test
     */
    public function it_decode_cookie(): void
    {
        $key = new RecallerKey('foo');
        $encoder = new CookieSecurity($key);

        $value = base64_encode('foobar');

        $this->assertTrue($this->isBase64($value));

        $decoded = $encoder->decode($value);

        $this->assertEquals('foobar', $decoded);
    }

    /**
     * @test
     */
    public function it_compare_hashes(): void
    {
        $key = new RecallerKey('foo');
        $encoder = new CookieSecurity($key);
        $values = ['foobar', 'bar'];

        $hash = $encoder->encode($values);

        $this->assertTrue($this->isBase64($hash));

        $this->assertTrue($encoder->compare($values, $hash));
    }

    private function isBase64(string $value): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value);
    }
}