<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;
use App\Domain\ValueObject\AbstractStringValueObject;

readonly class Uuid extends AbstractStringValueObject
{
    private const string UUID_V4_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    private function __construct(string $value,) {
        parent::__construct($value);
    }

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

}