<?php
declare(strict_types=1);


namespace App\Domain\Exception;
use App\Domain\Exception\DomainException;


final class InvalidPhoneException extends DomainException 
{

    public static function withValue(string $phone) : self 
    {
        return new self(
            sprintf('The value "%s" is not a valid E.164 phone number.', $phone)
        );
    }
}

?>