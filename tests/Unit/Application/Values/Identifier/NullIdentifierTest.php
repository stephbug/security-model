<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Values\Identifier\NullIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class NullIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_null_value(): void
    {
        $id = new NullIdentifier();

        $this->assertNull($id->identify());
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $id = new NullIdentifier();
        $id2 = new NullIdentifier();

        $this->assertTrue($id->sameValueAs($id2));
    }
}