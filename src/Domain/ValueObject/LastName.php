<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidLastNameException;
use Override;
use Stringable;

readonly class LastName implements Stringable
{

    private const string VALID_CHARACTERS = "/^[\p{L}\p{M}' -]+$/u";

    private function __construct(private string $value){}

    #[\NoDiscard]
    public static function Create(string $value) : self {
        $norm = strtolower(trim($value));

        if (empty($norm) || !preg_match(self::VALID_CHARACTERS, $norm)) {
            throw InvalidLastNameException::withValue($value);
        }

        return new self($norm);
    }

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

} 
?>