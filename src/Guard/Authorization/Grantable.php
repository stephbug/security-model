<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization;

interface Grantable
{
    public function isGranted(array $attributes = null, $object = null): bool;

    public function forceAuthentication(bool $force): Grantable;
}