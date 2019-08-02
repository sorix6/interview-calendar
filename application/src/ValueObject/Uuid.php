<?php declare(strict_types=1);

namespace InterviewCalendar\ValueObject;

use InterviewCalendar\ValueObject\Exception;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    private $uuid;

    public function __construct(string $value = null)
    {
        if (empty($value)){
            $this->uuid = RamseyUuid::uuid4();
        }
        else{
            $this->uuid = $this->validate($value);
        }
        

    }

    public function validate(string $value): RamseyUuid
    {
        if (!RamseyUuid::isValid($value) || RamseyUuid::fromString($value)->getVersion() != '4'){
            throw new Exception\InvalidUuid('Invalid user UUID', 400);
        }

        return RamseyUuid::fromString($value);
    }

    public function __toString(): string
    {
        return (string) $this->uuid;
    }
} 