<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\User;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\User\ClearPassword;
use StephBugTest\SecurityModel\Unit\TestCase;

class ClearPasswordTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $v = new ClearPassword('_foo_bar');

        $this->assertInstanceOf(ClearPassword::class, $v);
        $this->assertInstanceOf(Credentials::class, $v);
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $v = new ClearPassword('_foo_bar');

        $this->assertEquals('_foo_bar', $v->credentials());
        $this->assertEquals('_foo_bar', (string)$v);
    }

    /**
     * @test
     */
    public function it_can_be_compared_as_string(): void
    {
        $v = new ClearPassword('_foo_bar');

        $v1 = new ClearPassword('_baz_baz');
        $v2 = new ClearPassword('_foo_bar');

        $this->assertFalse($v->sameValueAs($v1));
        $this->assertTrue($v->sameValueAs($v2));
    }


    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed
     * @dataProvider provideNonStringPassword
     */
    public function it_raise_exception_when_clear_password_is_invalid($credential): void
    {
        $this->expectExceptionMessage('Password is invalid.');

        new ClearPassword($credential);
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed
     * @dataProvider provideInvalidLengthPassword
     */
    public function it_raise_exception_when_clear_password_length_is_invalid($credential): void
    {
        $message = sprintf('Password must be between %s and %s',
            ClearPassword::MIN_LENGTH, ClearPassword::MAX_LENGTH);

        $this->expectExceptionMessage($message);

        new ClearPassword($credential);
    }

    public function provideNonStringPassword(): array
    {
        return [
            [null],[new \stdClass()], [new class(){}], [[]]
        ];
    }

    public function provideInvalidLengthPassword(): array
    {
        return [
            [''],
            ['a'],
            [str_random(ClearPassword::MIN_LENGTH -1)],
            [str_random(ClearPassword::MAX_LENGTH +1)]
        ];
    }
}