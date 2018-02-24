<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

interface UserThrottle
{
    public function isEnabled(): bool;

    public function isNonLocked(): bool;
}