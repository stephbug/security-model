<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\User;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;
use StephBug\SecurityModel\Application\Values\User\ClearPasswordWithConfirmation;
use StephBugTest\SecurityModel\Unit\TestCase;

class ClearPasswordWithConfirmationTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $v = new ClearPasswordWithConfirmation('_foo_bar', '_foo_bar');

        $this->assertInstanceOf(ClearPasswordWithConfirmation::class, $v);
        $this->assertInstanceOf(Credentials::class, $v);
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $v = new ClearPasswordWithConfirmation('_foo_bar', '_foo_bar');

        $this->assertEquals('_foo_bar', $v->credentials());
        $this->assertEquals('_foo_bar', (string)$v);
    }

    /**
     * @test
     */
    public function it_can_be_compared_as_string(): void
    {
        $v = new ClearPasswordWithConfirmation('_foo_bar', '_foo_bar');

        $v1 = new ClearPasswordWithConfirmation('_baz_baz', '_baz_baz');
        $v2 = new ClearPasswordWithConfirmation('_foo_bar', '_foo_bar');

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

        new ClearPasswordWithConfirmation($credential, '_foo_bar');
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed
     * @dataProvider provideInvalidLengthPassword
     */
    public function it_raise_exception_when_clear_password_length_is_invalid($credential): void
    {
        $message = sprintf('Password must be between %s and %s',
            ClearPasswordWithConfirmation::MIN_LENGTH, ClearPasswordWithConfirmation::MAX_LENGTH);

        $this->expectExceptionMessage($message);

        new ClearPasswordWithConfirmation($credential, '_foo_bar');
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed
     * @dataProvider provideInvalidConfirmationPassword
     */
    public function it_raise_exception_when_clear_password_and_his_confirmation_does_not_match($credential): void
    {
        $this->expectExceptionMessage('Password confirmation does not match');

        new ClearPasswordWithConfirmation('_foo_bar', $credential);
    }

    public function provideNonStringPassword(): array
    {
        return [
            [null], [new \stdClass()], [new class()
            {
            }], [[]]
        ];
    }

    public function provideInvalidLengthPassword(): array
    {
        return [
            [''],
            ['a'],
            [str_random(ClearPasswordWithConfirmation::MIN_LENGTH - 1)],
            [str_random(ClearPasswordWithConfirmation::MAX_LENGTH + 1)]
        ];
    }

    public function provideInvalidConfirmationPassword(): array
    {
        return [
            [null], [new \stdClass()], [new class(){}], [[]], [''], ['baz']
        ];
    }
}