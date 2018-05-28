<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Mock;

class SomeAuthEvent
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}