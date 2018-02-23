<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Role;

interface RoleSecurity
{
    public function getRole(): string;

    public function __toString(): string;
}