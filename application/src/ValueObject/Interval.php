<?php declare(strict_types=1);

namespace InterviewCalendar\ValueObject;

use InterviewCalendar\ValueObject\Exception;

class Interval implements \JsonSerializable
{
    private $start;
    private $end;
    private $step;

    public function __construct(int $start, int $end, int $step = null)
    {
        $this->validate($start, $end);

        $this->start = $start;
        $this->end = $end;
        $this->step = $step ?? 1;
    }

    private function validate(int $start, int $end, int $step = null)
    {
        if ($start < 0 || $start > 23){
            throw new InvalidIntervalBorder('The start of the time interval must start between 0 and 23', 400);
        }
        else if ($end < 1 || $end > 24){
            throw new InvalidIntervalBorder('The end of the time interval must start between 0 and 23', 400);
        }

        if (!empty($step) && $step > ($end - $start)){
            throw new InvalidIntervalStep('The step for the interval cannot be larger than the length  of the interval', 400);
        }
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function intersect(Interval $interval): array
    {
        return array_intersect($this->range(), $interval->range());
    }

    public function range(): array
    {
        return range($this->start, $this->end - 1, $this->step);
    }

    public function __toString(): string 
    {
        return '[' . $this->start() . ',' . $this->end() . ')';
    }

    public function jsonSerialize(): string
    {
        return $this->start() . ' - ' . $this->end();
    }
}