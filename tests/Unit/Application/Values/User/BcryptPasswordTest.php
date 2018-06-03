<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Values\User;

use StephBug\SecurityModel\Application\Values\Contract\EncodedPassword;
use StephBug\SecurityModel\Application\Values\User\BcryptPassword;
use StephBugTest\SecurityModel\Unit\TestCase;

class BcryptPasswordTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_constructed(): void
    {
        $encoded = password_hash('foobar', PASSWORD_BCRYPT);

        $v = new BcryptPassword($encoded);

        $this->assertInstanceOf(BcryptPassword::class, $v);
        $this->assertInstanceOf(EncodedPassword::class, $v);
    }

    /**
     * @test
     */
    public function it_can_be_serialized(): void
    {
        $encoded = password_hash('foobar', PASSWORD_BCRYPT);

        $v = new BcryptPassword($encoded);

        $this->assertEquals($encoded, $v->credentials());
        $this->assertEquals($encoded, (string)$v);
    }

    /**
     * @test
     */
    public function it_can_be_compared_as_string(): void
    {
        $encoded = password_hash('foobar', PASSWORD_BCRYPT);
        $v = new BcryptPassword($encoded);

        $v1 = new BcryptPassword(password_hash('foobar', PASSWORD_BCRYPT));
        $v2 = new BcryptPassword(password_hash('baz', PASSWORD_BCRYPT));

        $this->assertFalse($v->sameValueAs($v1));
        $this->assertFalse($v->sameValueAs($v2));

        $this->assertTrue($v->sameValueAs($v));
    }



    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\Assert\SecurityValueFailed
     */
    public function it_raise_exception_when_encoded_password_is_invalid(): void
    {
        $this->expectExceptionMessage('Invalid credential');

        new BcryptPassword('foobar');
    }
}