<?php
declare(strict_types=1);


namespace App\Domain\Exception;
use App\Domain\Exception\DomainException;


final class ContactNotFoundException extends DomainException 
{

    public static function withValue(string $contact) : self 
    {
        return new self(
            sprintf('Contact "%s" not found.', $contact)
        );
    }
}

?>