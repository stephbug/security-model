<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\User;

interface UserRecaller
{
    public function getRecallerToken(): ?string;
}