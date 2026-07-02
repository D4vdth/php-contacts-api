<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidEmailException;
use App\Domain\ValueObject\AbstractStringValueObject;


readonly class Email extends AbstractStringValueObject
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

}