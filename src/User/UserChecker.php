<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

interface UserChecker
{
    public function onPreAuthentication(UserSecurity $user): void;

    public function onPostAuthentication(UserSecurity $user): void;
}