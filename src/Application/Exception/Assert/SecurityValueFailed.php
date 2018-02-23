<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Exception\Assert;

use StephBug\SecurityModel\Application\Exception\AuthenticationException;

class SecurityValueFailed extends AuthenticationException
{
    private $propertyPath;
    private $value;
    private $constraints;

    public function __construct($message, $code, $propertyPath, $value, array $constraints = array())
    {
        parent::__construct($message, $code);

        $this->propertyPath = $propertyPath;
        $this->value = $value;
        $this->constraints = $constraints;
    }

    public function getPropertyPath()
    {
        return $this->propertyPath;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }
}