<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidLastNameException;
use App\Domain\ValueObject\AbstractStringValueObject;

readonly class LastName extends AbstractStringValueObject
{
    protected const string VALID_CHARACTERS = "/^[\p{L}\p{M}' -]+$/u";

    private function __construct(string $value){
        parent::__construct($value);
    }

    #[\NoDiscard]
    public static function create(string $value) : self {
        $norm = trim($value);
        $leng = mb_strlen($norm);

        if (empty($norm) || !preg_match(self::VALID_CHARACTERS, $norm) || $leng < 2 || $leng > 100) {
            throw InvalidLastNameException::withValue($value);
        }

        return new self($norm);
    }

    public static function fromExisting(string $value) : self 
    {
        return new self($value);
    }

}