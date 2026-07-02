<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;
use Stringable;

readonly class Uuid implements Stringable
{
    private const string UUID_V4_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    private function __construct(
        private string $value,
    ) {}

    public static function generate(): self
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        $hex = bin2hex($bytes);

        return new self(
            sprintf(
                '%s-%s-%s-%s-%s',
                substr($hex, 0, 8),  
                substr($hex, 8, 4),   
                substr($hex, 12, 4),  
                substr($hex, 16, 4),  
                substr($hex, 20, 12), 
            )
        );
    }

    public static function fromString(string $value): self
    {
        $normalized = strtolower(trim($value));

        if (preg_match(self::UUID_V4_PATTERN, $normalized) !== 1) {
            throw new InvalidArgumentException(
                sprintf('The value "%s" is not a valid UUID v4.', $value)
            );
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}