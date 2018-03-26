<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UserToken;
use StephBug\SecurityModel\Application\Values\Identifier\EmailIdentifier as EmailId;

class UserSecurity implements \StephBug\SecurityModel\User\UserSecurity, UserToken
{
    /**
     * @var UuidInterface
     */
    private $uid;

    /**
     * @var bool
     */
    private $sameValueAs;

    public function __construct(UuidInterface $uid, bool $sameValueAs)
    {
        $this->uid = $uid;
        $this->sameValueAs = $sameValueAs;
    }

    public function getIdentifier(): SecurityIdentifier
    {
        return $this->getId();
    }

    public function getId(): UniqueIdentifier
    {
        return new UserSecurityId($this->uid, $this->sameValueAs);
    }

    public function getEmail(): EmailIdentifier
    {
        return EmailId::fromString('foobar@bar.com');
    }

    public function getRoles(): Collection
    {
        return new Collection();
    }
}