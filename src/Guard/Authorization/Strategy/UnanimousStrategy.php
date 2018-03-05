<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Strategy;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authorization\Voter\Votable;

class UnanimousStrategy implements AuthorizationStrategy
{
    /**
     * @var array[Votable]
     */
    private $voters;

    /**
     * @var bool
     */
    private $allowIfAllAbstain = false;

    public function __construct(array $voters)
    {
        $this->voters = $voters;
    }

    public function decide(Tokenable $token, array $attributes, $object = null): bool
    {
        $grant = 0;

        foreach ($attributes as $attribute) {
            foreach ($this->voters as $voter) {
                $decision = $voter->vote($token, [$attribute], $object);

                switch ($decision) {
                    case Votable::ACCESS_GRANTED:
                        ++$grant;
                        break;
                    case Votable::ACCESS_DENIED:
                        return false;
                    default:
                        break;
                }
            }
        }

        return ($grant > 0) ? true : $this->allowIfAllAbstain;
    }

    public function setAllowIfAllAbstain(bool $allowIfAllAbstain): void
    {
        $this->allowIfAllAbstain = $allowIfAllAbstain;
    }
}