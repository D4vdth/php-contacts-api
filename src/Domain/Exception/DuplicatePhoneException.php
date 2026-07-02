<?php
declare(strict_types=1);


namespace App\Domain\Exception;
use App\Domain\Exception\DomainException;


final class DuplicatePhoneException extends DomainException 
{

    public static function withValue(string $phone) : self 
    {
        return new self(
            sprintf('The value "%s" is a duplicated phone number.', $phone)
        );
    }
}

?>