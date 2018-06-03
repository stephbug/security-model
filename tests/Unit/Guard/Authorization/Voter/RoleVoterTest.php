<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Guard\Authorization\Voter;

use StephBug\SecurityModel\Application\Values\Role\RoleValue;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Voter\RoleVoter;
use StephBug\SecurityModel\Guard\Authorization\Voter\Votable;
use StephBugTest\SecurityModel\Unit\TestCase;

class RoleVoterTest extends TestCase
{
    /**
     * @test
     */
    public function it_grant_access_with_extracted_roles_from_token(): void
    {
        $v = new RoleVoter();

        $roles = [new RoleValue('ROLE_FOO')];
        $token = $this->getMockForAbstractClass(Tokenable::class);
        $token->expects($this->once())->method('getRoles')->willReturn($roles);

        $result = $v->vote($token, ['ROLE_FOO']);
        $this->assertEquals(Votable::ACCESS_GRANTED, $result);
    }

    /**
     * @test
     */
    public function it_deny_access_if_token_does_not_have_role(): void
    {
        $v = new RoleVoter();

        $roles = [new RoleValue('ROLE_BAR')];
        $token = $this->getMockForAbstractClass(Tokenable::class);
        $token->expects($this->once())->method('getRoles')->willReturn($roles);

        $result = $v->vote($token, ['ROLE_FOO']);
        $this->assertEquals(Votable::ACCESS_DENIED, $result);
    }

    /**
     * @test
     */
    public function it_abstain_if_role_is_not_supported(): void
    {
        $v = new RoleVoter();

        $roles = [new RoleValue('ROLE_FOO')];
        $token = $this->getMockForAbstractClass(Tokenable::class);
        $token->expects($this->once())->method('getRoles')->willReturn($roles);

        $result = $v->vote($token, ['BAR']);
        $this->assertEquals(Votable::ACCESS_ABSTAIN, $result);
    }
}