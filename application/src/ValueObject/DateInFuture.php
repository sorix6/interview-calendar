<?php declare(strict_types=1);

namespace InterviewCalendar\ValueObject;

use InterviewCalendar\ValueObject\Exception;

class DateInFuture
{
    private $value;

    public function __construct(string $date){

        $this->value = $this->validate($date);

    }

    private function validate(string $date)
    {
        $dateObject = new DateTime($date);
        $currentDate = new DateTime;

        if ($dateObject < $currentDate){
            throw InvalidDate('The date cannot be in the past', 400);
        }

        return $dateObject;
    }

    public function value(): DateTime 
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value->format('Y-m-d');
    }
}