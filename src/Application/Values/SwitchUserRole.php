<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class SwitchUserRole extends RoleValue
{
    /**
     * @var Tokenable
     */
    private $source;

    public function __construct(string $role, Tokenable $source)
    {
        parent::__construct($role);

        $this->source = $source;
    }

    public function source(): Tokenable
    {
        return $this->source;
    }
}