<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values;

use StephBug\SecurityModel\Application\Exception\Assert\Secure;

class ClearPasswordWithConfirmation extends ClearPassword
{
    public function __construct($password, $passwordConfirmation)
    {
        parent::__construct($password);

        Secure::same($password, $passwordConfirmation, 'Password confirmation does not match');
    }
}