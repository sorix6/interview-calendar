<?php declare(strict_types=1);

namespace InterviewCalendar\Model;

use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\EmailAddress;


use Ramsey\Uuid\Uuid;

class Account implements AccountInterface, \JsonSerializable
{

    private $uuid;
    private $lastname;
    private $firstname;
    private $email;

    public function __construct(Uuid $uuid, string $firstname, string $lastname, EmailAddress $email)
    {
        $this->uuid = $uuid;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
    }

    public function uuid(): Uuid
    {
        return $this->uuid;
    }

    public function lastname(): string
    {
        return $this->lastname;
    }

    public function firstname(): string
    {
        return $this->firstname;
    }

    public function email(): EmailAddress
    {
        return $this->email;
    }

    public function jsonSerialize(): array
    {
        return array(
            'uuid'      => (string) $this->uuid,
            'fullname'  => $this->firstname . ' ' . $this->lastname,
            'email'     => (string) $this->email
        );
    }

}