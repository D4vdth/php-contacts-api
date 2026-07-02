<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidEmailException;
use Override;
use Stringable;

readonly class Email implements Stringable
{

    private function __construct(private string $value){}

    #[\NoDiscard]
    public static function create(string $value) : self {
        $norm = strtolower(trim($value));

        if (filter_var($norm, FILTER_VALIDATE_EMAIL) === false) {
            throw InvalidEmailException::withValue($value);
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