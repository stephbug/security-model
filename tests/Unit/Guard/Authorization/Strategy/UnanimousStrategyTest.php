<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authorization\Strategy;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Strategy\UnanimousStrategy;
use StephBug\SecurityModel\Guard\Authorization\Voter\Votable;
use StephBugTest\SecurityModel\Unit\TestCase;

class UnanimousStrategyTest extends TestCase
{
    /**
     * @test
     */
    public function it_disallow_access_with_no_voters(): void
    {
        $s = $this->getStrategyInstance();

        $ref = new \ReflectionClass($s);
        $prop = $ref->getProperty('allowIfAllAbstain');
        $prop->setAccessible(true);

        $this->assertFalse($prop->getValue($s));
        $this->assertFalse($s->decide($this->token, ['foo']));
    }

    /**
     * @test
     */
    public function it_disallow_access_with_no_attribute(): void
    {
        $voter = $this->getMockForAbstractClass(Votable::class);
        $voter->expects($this->never())->method('vote');

        $s = $this->getStrategyInstance([$voter]);

        $ref = new \ReflectionClass($s);
        $prop = $ref->getProperty('allowIfAllAbstain');
        $prop->setAccessible(true);

        $this->assertFalse($prop->getValue($s));
        $this->assertFalse($s->decide($this->token, []));
    }

    /**
     * @test
     */
    public function it_deny_access_if_result_voters_abstain(): void
    {
        $voter = $this->getMockForAbstractClass(Votable::class);
        $voter->expects($this->once())->method('vote')->willReturn(0);

        $s = $this->getStrategyInstance([$voter]);

        $ref = new \ReflectionClass($s);
        $prop = $ref->getProperty('allowIfAllAbstain');
        $prop->setAccessible(true);

        $this->assertFalse($prop->getValue($s));
        $this->assertFalse($s->decide($this->token, ['foo']));
    }

    /**
     * @test
     */
    public function it_grant_access_if_result_voters_abstain(): void
    {
        $voter = $this->getMockForAbstractClass(Votable::class);
        $voter->expects($this->once())->method('vote')->willReturn(0);

        $s = $this->getStrategyInstance([$voter]);

        $ref = new \ReflectionClass($s);
        $prop = $ref->getProperty('allowIfAllAbstain');
        $prop->setAccessible(true);

        $this->assertFalse($prop->getValue($s));
        $s->setAllowIfAllAbstain(true);
        $this->assertTrue($prop->getValue($s));

        $this->assertTrue($s->decide($this->token, ['foo']));
    }

    /**
     * @test
     */
    public function it_grant_access_when_no_voter_deny(): void
    {
        $voter = $this->getMockForAbstractClass(Votable::class);
        $voter->expects($this->once())->method('vote')->willReturn(1);

        $s = $this->getStrategyInstance([$voter]);
        $this->assertTrue($s->decide($this->token, ['foo']));
    }

    /**
     * @test
     */
    public function it_deny_access_with_one_failure_voter(): void
    {
        $voter = $this->getMockForAbstractClass(Votable::class);
        $voter->expects($this->once())->method('vote')->willReturn(1);

        $voter2 = $this->getMockForAbstractClass(Votable::class);
        $voter2->expects($this->once())->method('vote')->willReturn(-1);

        $s = $this->getStrategyInstance([$voter, $voter2]);
        $this->assertFalse($s->decide($this->token, ['foo']));
    }

    /**
     * @test
     */
    public function it_deny_access_immediately_with_failure_voters(): void
    {
        $voter = $this->getMockForAbstractClass(Votable::class);
        $voter->expects($this->once())->method('vote')->willReturn(-1);

        $voter2 = $this->getMockForAbstractClass(Votable::class);
        $voter2->expects($this->never())->method('vote');

        $s = $this->getStrategyInstance([$voter, $voter2]);
        $this->assertFalse($s->decide($this->token, ['foo']));
    }

    public function getStrategyInstance(array $voters = []): UnanimousStrategy
    {
        return new UnanimousStrategy($voters);
    }

    private $token;
    protected function setUp()
    {
        $this->token = $this->getMockForAbstractClass(Tokenable::class);
    }
}