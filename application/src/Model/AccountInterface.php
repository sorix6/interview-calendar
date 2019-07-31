<?php declare(strict_types=1);

namespace InterviewCalendar\Model;

use InterviewCalendar\ValueObject\Exception;
use InterviewCalendar\ValueObject\EmailAddress;

use Ramsey\Uuid\Uuid;

interface AccountInterface
{
    public function uuid(): Uuid;

    public function lastname(): string;

    public function firstname(): string;

    public function email(): EmailAddress;
}