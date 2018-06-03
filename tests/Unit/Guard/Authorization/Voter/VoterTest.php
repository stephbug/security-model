<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Voter\Votable;
use StephBug\SecurityModel\Guard\Authorization\Voter\Voter;
use StephBugTest\SecurityModel\Unit\TestCase;

class VoterTest extends TestCase
{
    /**
     * @test
     */
    public function it_grant_access_with_supported_attributes(): void
    {
        $v = $this->getFooVoter('foo_edit', true);
        $token = $this->getMockForAbstractClass(Tokenable::class);

        $result = $v->vote($token, ['foo_edit']);
        $this->assertEquals(Votable::ACCESS_GRANTED, $result);
    }

    /**
     * @test
     */
    public function it_deny_access(): void
    {
        $v = $this->getFooVoter('foo_edit', false);

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $result = $v->vote($token, ['foo_edit']);
        $this->assertEquals(Votable::ACCESS_DENIED, $result);
    }

    /**
     * @test
     */
    public function it_abstain_if_attribute_is_not_supported(): void
    {
        $v = $this->getFooVoter('foo_edit', true); // 2n parameter should not be called

        $token = $this->getMockForAbstractClass(Tokenable::class);

        $result = $v->vote($token, ['BAR']);
        $this->assertEquals(Votable::ACCESS_ABSTAIN, $result);
    }

    private function getFooVoter(string $expected, bool $result): Voter
    {
        return new class($expected, $result) extends Voter
        {
            private $expected;
            private $result;

            public function __construct(string $expected, bool $result)
            {
                $this->expected = $expected;
                $this->result = $result;
            }

            protected function supports(string $attribute, $subject): bool
            {
                return $attribute === $this->expected;
            }

            protected function voteOn(string $attribute, Tokenable $token, $subject): bool
            {
                return $this->result;
            }
        };
    }
}