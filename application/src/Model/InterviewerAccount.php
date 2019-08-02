<?php declare(strict_types=1);

namespace InterviewCalendar\Model;

use Ramsey\Uuid\Uuid;

class InterviewerAccount extends Account
{

    public function toArray(): array
    {
        return [
            'uuid'      => (string) $this->uuid,
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'email'     => (string) $this->email,
            'type'      => 1
        ];
    }
}