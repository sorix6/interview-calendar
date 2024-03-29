<?php declare(strict_types=1);

namespace InterviewCalendar\Model;

use InterviewCalendar\ValueObject\DateInFuture;
use InterviewCalendar\ValueObject\Interval;


use Ramsey\Uuid\Uuid;

class Availability implements \JsonSerializable
{
    private $date;
    private $intervals;

    public function __construct(DateInFuture $date, array $intervals = array())
    {
        $this->date = $date;
        $this->intervals = $intervals;
    }

    public function addInterval(Interval $interval)
    {
        array_push($this->intervals, $interval);
    }

    public function date(): string
    {
        return (string) $this->date;
    }

    public function intervals(): array
    {
        return $this->intervals;
    }

    public function range(): array
    {
        $range = [];
        foreach($this->intervals as $interval)
        {
            $range = array_merge($range, $interval->range());
        }

        $range = array_unique($range);
        sort($range);

        return [ 'date' => (string) $this->date, 'range' => $range ];
    }

    public function toArray(): array
    {
        return [
            'date' => (string) $this->date, 
            'intervals' => $this->intervals
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

}