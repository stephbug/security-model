<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\Identifier;

use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class EmailIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_new_instance(): void
    {
        $em = EmailIdentifier::fromString('foo@bar.com');
        $em1 = EmailIdentifier::fromString('bar@bar.com');

        $this->assertNotEquals($em, $em1);
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $em = EmailIdentifier::fromString('foo@bar.com');
        $this->assertEquals('foo@bar.com', $em->identify());
    }

    /**
     * @test
     */
    public function it_can_be_compared(): void
    {
        $em = EmailIdentifier::fromString('foo@bar.com');
        $em1 = EmailIdentifier::fromString('bar@bar.com');

        $this->assertFalse($em->sameValueAs($em1));
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed
     * @dataProvider provideInvalidEmail
     */
    public function it_raise_exception_when_email_is_not_valid($email): void
    {
        EmailIdentifier::fromString($email);
    }

    public function provideInvalidEmail(): array
    {
        return [
            [null], [''], ['foo'], [new \stdClass()]
        ];
    }
}