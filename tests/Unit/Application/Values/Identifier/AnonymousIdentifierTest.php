<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Values\Identifier\AnonymousIdentifier;
use StephBugTest\SecurityModel\Mock\SomeAnonymousIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class AnonymousIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_identify(): void
    {
        $id = new AnonymousIdentifier();

        $this->assertEquals('anon', $id->identify());
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $id = new AnonymousIdentifier();
        $id2 = new AnonymousIdentifier();
        $id3 = new SomeAnonymousIdentifier();

        $this->assertTrue($id->sameValueAs($id2));
        $this->assertFalse($id->sameValueAs($id3));
    }
}