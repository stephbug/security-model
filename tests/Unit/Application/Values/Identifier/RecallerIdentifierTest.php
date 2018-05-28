<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Values\Identifier\RecallerIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class RecallerIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_generate_random_base_64_recaller(): void
    {
        $id = RecallerIdentifier::nextIdentity();
        $id2 = RecallerIdentifier::nextIdentity();

        $this->assertNotEmpty($id);
        $this->assertNotEquals($id, $id2);

        $this->assertTrue($this->isBase64($id->identify()));
    }

    /**
     * @test
     */
    public function it_can_be_serialize(): void
    {
        $id = RecallerIdentifier::fromString('foobar');

        $this->assertEquals('foobar', $id->identify());
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $id = RecallerIdentifier::nextIdentity();
        $id2 = RecallerIdentifier::nextIdentity();

        $this->assertTrue($id->sameValueAs($id));
        $this->assertFalse($id->sameValueAs($id2));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     * @dataProvider
     */
    public function it_raise_exception_when_recaller_is_invalid($value = null): void
    {
        RecallerIdentifier::fromString($value);
    }

    public function provideInvalidRecallerValue()
    {
        return [[null], [''], [new \stdClass()]];
    }

    private function isBase64(string $value): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value);
    }
}