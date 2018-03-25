<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Values\User;

use StephBug\SecurityModel\Application\Values\Contract\Credentials;

abstract class Password implements Credentials
{
    /**
     * @var string
     */
    protected $password;

    public function __construct($password)
    {
        if (is_string($password)) {
            $password = trim($password);
        }

        $this->validate($password);

        $this->password = $password;
    }

    abstract protected function validate($password): void;

    public function __toString(): string
    {
        return $this->password;
    }
}