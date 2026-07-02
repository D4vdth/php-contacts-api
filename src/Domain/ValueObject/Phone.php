<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;
use App\Domain\Exception\InvalidPhoneException;
use Override;
use Stringable;

readonly class Phone implements Stringable
{
    private const string E164_PATTERN = '/^\+[1-9]\d{6,14}$/';

    private function __construct(private string $value){}

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