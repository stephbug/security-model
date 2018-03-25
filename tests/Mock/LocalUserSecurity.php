<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

use StephBug\SecurityModel\Application\Values\Contract\EncodedPassword;
use StephBug\SecurityModel\Application\Values\User\BcryptPassword;
use StephBug\SecurityModel\User\LocalUser;

class LocalUserSecurity extends UserSecurity implements LocalUser
{
    public function getPassword(): EncodedPassword
    {
        return new BcryptPassword(bcrypt('foobar'));
    }

    public function eraseCredentials(): void
    {

    }
}