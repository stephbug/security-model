<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User\Recaller;

use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier;
use StephBug\SecurityModel\User\UserSecurity;

interface UserRecallerProviderRead
{
    public function requireByRecallerIdentifier(RecallerIdentifier $identifier): UserSecurity;
}