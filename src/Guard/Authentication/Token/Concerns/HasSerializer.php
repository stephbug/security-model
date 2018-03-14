<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authentication\Token\Concerns;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

trait HasSerializer
{
    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        [
            $this->user,
            $this->authenticated,
            $this->roles,
            $this->attributes
        ] = unserialize($serialized, [Tokenable::class]);
    }

    public function toArray(): array
    {
        return [
            is_object($this->user) ? clone $this->user : $this->user,
            $this->authenticated,
            array_map(function ($role) {return clone $role;}, $this->roles),
            $this->attributes
        ];
    }

    public function toJson($options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->toJson();
    }
}