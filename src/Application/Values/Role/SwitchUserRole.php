<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\Role;

use StephBug\SecurityModel\Application\Values\Contract\SecurityValue;
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

    public function sameValueAs(SecurityValue $aValue): bool
    {
        return $aValue instanceof $this
            && $this->getRole() === $aValue->getRole()
            && $this->source->getIdentifier()->sameValueAs($aValue->source()->getIdentifier());
    }
}