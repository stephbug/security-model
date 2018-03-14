<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

trait HasAttributes
{
    /**
     * @var array
     */
    private $attributes = [];

    public function setAttribute(string $attribute, $value): void
    {
        $this->attributes[$attribute] = $value;
    }

    public function getAttribute(string $attribute, $default = null)
    {
        if ($this->hasAttribute($attribute)) {
            return $this->attributes[$attribute];
        }

        return $default;
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function forgetAttribute(string $attribute): bool
    {
        if ($this->hasAttribute($attribute)) {
            unset($this->attributes[$attribute]);

            return true;
        }

        return false;
    }

    public function getAttributes(): iterable
    {
        return $this->attributes;
    }
}