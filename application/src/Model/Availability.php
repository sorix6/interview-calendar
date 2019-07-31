<?php declare(strict_types=1);

namespace InterviewCalendar\Model;

use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\DateInFuture;
use InterviewCalendar\ValueObject\Interval;


use Ramsey\Uuid\Uuid;

class Availability 
{
    private $account;
    private $date;
    private $interval;

    public function __construct(AccountInterface $account, DateInFuture $date, Interval $interval)
    {
        $this->account = $account;
        $this->date = $date;
        $this->interval = $interval;
    }
}
    public function accountUuid(): Uuid
    {
        return $this->account->uuid();
    }

    public function date(): string
    {
        return $this->date->value();
    }

    public function intervalStart(): int
    {
        return $this->interval->start();
    }

    public function intervalEnd(): int
    {
        return $this->interval->end();
    }

}