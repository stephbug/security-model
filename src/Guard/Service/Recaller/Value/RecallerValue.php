<?php

namespace StephBug\SecurityModel\Guard\Service\Recaller\Value;

interface RecallerValue
{
    public function id(): string;

    public function token(): string;

    public function hash(): string;

    public function delimiter(): string;

    public function valid(): bool;

    public function toArray(): array;
}