<?php declare(strict_types=1);

namespace InterviewCalendar\ValueObject;

use InterviewCalendar\ValueObject\Exception;

class EmailAddress 
{
    private $value;

    public function __construct(string $emailAddress){

        $this->validate($emailAddress);

        $this->value = $emailAddress;
    }

    private function validate(string $emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)){
            throw new Exception\InvalidEmailAddress('Invalid email address', 400);
        }
    }

    public function __toString(): string 
    {
        return $this->value;
    }

}