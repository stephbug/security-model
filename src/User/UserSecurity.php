<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

use Illuminate\Support\Collection;
use StephBug\SecurityModel\Application\Values\Contract\EmailAddress as EmailContract;
use StephBug\SecurityModel\Application\Values\Contract\SecurityIdentifier;
use StephBug\SecurityModel\Application\Values\Contract\UniqueIdentifier;

interface UserSecurity
{
    public function getIdentifier(): SecurityIdentifier;

    public function getId(): UniqueIdentifier;

    public function getEmail(): EmailContract;

    public function getRoles(): Collection;
}