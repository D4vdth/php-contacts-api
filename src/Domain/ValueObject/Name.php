<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidNameException;
use App\Domain\ValueObject\AbstractStringValueObject;

readonly class Name extends AbstractStringValueObject
{

    private function __construct(private string $value){}

    #[\NoDiscard]
    public static function create(string $value) : self {
        $norm = trim($value);
        $leng = mb_strlen($value);

        if (empty($norm) || !preg_match(parent::validCharacters(), $norm) || $leng >= 3 || $leng <= 100) {
            throw InvalidNameException::withValue($value);
        }

        return new self($norm);
    }

    public static function fromExisting(string $value) : self 
    {
        return new self($value);
    }

} 