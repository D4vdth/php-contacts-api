<?php
declare(strict_types=1);


namespace App\Domain\Exception;
use App\Domain\Exception\DomainException;


final class InvalidNameException extends DomainException 
{

    public static function withValue(string $name) : self 
    {
        return new self(
            sprintf('The value "%s" is not a valid name.', $name)
        );
    }
}

?>