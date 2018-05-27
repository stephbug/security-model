<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller\Value;

class Recaller implements RecallerValue
{
    const DELIMITER = '|';

    /**
     * @var string
     */
    private $recaller;

    public function __construct(string $recaller)
    {
        $this->recaller = $recaller;
    }

    public function id(): string
    {
        return $this->extractRecallerValueAt(0);
    }

    public function token(): string
    {
        return $this->extractRecallerValueAt(1);
    }

    public function hash(): string
    {
        return $this->extractRecallerValueAt(2);
    }

    public function delimiter(): string
    {
        return self::DELIMITER;
    }

    public function valid(): bool
    {
        if (!str_contains($this->recaller, self::DELIMITER)) {
            return false;
        }

        $segments = explode(self::DELIMITER, $this->recaller);

        return 3 === count($segments)
            && trim($segments[0]) !== ''
            && trim($segments[1]) !== ''
            && trim($segments[2]) !== '';
    }

    public function toArray(): array
    {
        // without hash
        return [$this->id(), $this->token()];
    }

    private function extractRecallerValueAt(int $position): string
    {
        return explode(self::DELIMITER, $this->recaller, 3)[$position];
    }
}