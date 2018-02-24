<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

use StephBug\SecurityModel\Application\Values\Contract\EncodedPassword;

interface LocalUser extends UserSecurity
{
    public function getPassword(): EncodedPassword;
}