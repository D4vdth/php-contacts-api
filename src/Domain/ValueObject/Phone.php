<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidPhoneException;
use App\Domain\ValueObject\AbstractStringValueObject;

readonly class Phone extends AbstractStringValueObject
{
    private const string E164_PATTERN = '/^\+[1-9]\d{6,14}$/';

    private function __construct(string $value){
        parent::__construct($value);
    }

    #[\NoDiscard]
    public static function create(string $value) : self 
    {
            $norm = trim($value);

            if(preg_match(self::E164_PATTERN, $norm) !== 1)
                {
                    throw InvalidPhoneException::withValue($value);
                }

            return new self($norm);
    }

    public static function fromExisting(string $value) : self 
    {
        return new self($value);
    }

} 