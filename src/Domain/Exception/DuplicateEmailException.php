<?php
declare(strict_types=1);


namespace App\Domain\Exception;
use App\Domain\Exception\DomainException;


final class DuplicateEmailException extends DomainException 
{

    public static function withValue(string $email) : self 
    {
        return new self(
            sprintf('The value "%s" is a duplicated email.', $email)
        );
    }
}

?>