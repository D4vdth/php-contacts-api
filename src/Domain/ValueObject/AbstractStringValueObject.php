<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Override;
use Stringable;

abstract readonly class AbstractStringValueObject implements Stringable
{
    private const string VALID_CHARACTERS = "/^[\p{L}\p{M}' -]+$/u";

    private function __construct(private string $value) {}

    public function value(): string 
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->value === $other->value;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }

    public function validCharacters(): string 
    {
        return self::VALID_CHARACTERS;
    }

}