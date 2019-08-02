<?php declare(strict_types=1);

namespace InterviewCalendar\Model;

use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\EmailAddress;
use InterviewCalendar\ValueObject\Uuid;


class Account implements AccountInterface, \JsonSerializable
{

    protected $uuid;
    protected $lastname;
    protected $firstname;
    protected $email;

    protected $availabilities = array();

    public function __construct(Uuid $uuid, string $firstname, string $lastname, EmailAddress $email)
    {
        $this->uuid = $uuid;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
    }

    public function addAvailability(Availability $availability)
    {
        array_push($this->availabilities, $availability);
    }

    public function getAvailabilities(): array
    {
        return $this->availabilities;
    }

    public function getAvailableDates(): array
    {
        $dates = [];
        foreach($this->availabilities as $availability){
            $dates[(string) $availability->date()] = sizeOf($availability->getIntervals());
        }

        return $dates;
    }

    public function setAvailabilities(array $availabilities)
    {
        $this->availabilities = $availabilities;
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
        return [
            'uuid'      => (string) $this->uuid,
            'fullname'  => $this->firstname . ' ' . $this->lastname,
            'email'     => (string) $this->email,
            'availability' => $this->availabilities
        ];
    }

    public function __toString(): string 
    {
        return (string) $this->uuid;
    }

}