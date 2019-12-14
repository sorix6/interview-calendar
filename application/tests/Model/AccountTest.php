<?php

use PHPUnit\Framework\TestCase;

use InterviewCalendar\Model\Account;
use InterviewCalendar\Model\CandidateAccount;
use InterviewCalendar\Model\InterviewerAccount;
use InterviewCalendar\Model\Availability;
use InterviewCalendar\ValueObject\Interval;
use InterviewCalendar\ValueObject\DateInFuture;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\EmailAddress;

class AccountTest extends TestCase
{
    
    public function setUp(): void
    {
        $this->stringUuid = '7112b944-d668-4137-9b30-4688e7b905f1';
        $this->uuid = new Uuid($this->stringUuid);
        $this->emailString = 'sorix@testing.com';
        $this->email = new EmailAddress($this->emailString);
        $this->firstname = 'Sorix';
        $this->lastname = 'Testing';

        $interval = $this->getMockBuilder(Interval::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->date1 = Date('Y-m-d', time() + 1 * 24 * 3600);
        $this->date2 = Date('Y-m-d', time() + 2 * 24 * 3600);
        $this->date3 = Date('Y-m-d', time() + 3 * 24 * 3600);
        
        $this->availability1 = new Availability(
            new DateInFuture($this->date1), 
            [$interval]
        );

        $this->availability2 = new Availability(
            new DateInFuture($this->date2), 
            [$interval]
        );

        $this->availability3 = new Availability(
            new DateInFuture($this->date3), 
            [$interval]
        );

    }

    public function testAccountCreation()
    {
        $account = new Account(
            $this->uuid,
            $this->firstname,
            $this->lastname,
            $this->email
        );

        $this->assertEquals((string) $account->uuid(), $this->stringUuid);
        $this->assertEquals($account->firstname(), $this->firstname);
        $this->assertEquals($account->lastname(), $this->lastname);
        $this->assertEquals((string) $account->email(), $this->emailString);
        $this->assertEquals((string) $account, $this->stringUuid);
        $this->assertEquals(
            $account->jsonSerialize(), 
            [
                'uuid'      => $this->stringUuid,
                'fullname'  => $this->firstname . ' ' . $this->lastname,
                'email'     => $this->emailString,
                'availability' => []
            ]
        );

    }

    public function testAddAvailability()
    {
        $account = new Account(
            $this->uuid,
            $this->firstname,
            $this->lastname,
            $this->email
        );

        $account->addAvailability($this->availability1);

        $this->assertCount(1, $account->getAvailabilities());
        $this->assertInstanceOf(Availability::class, $account->getAvailabilities()[0]);

        $account->addAvailability($this->availability2);

        $this->assertCount(2, $account->getAvailabilities());
        $this->assertInstanceOf(Availability::class, $account->getAvailabilities()[1]);

        $this->assertEquals(
            [
                (string) $this->date1 => 1,
                (string) $this->date2 => 1
            ],
            $account->getAvailableDates()
        );

    }


    public function testResetAvailabilitiesToNull()
    {
        $account = new Account(
            $this->uuid,
            $this->firstname,
            $this->lastname,
            $this->email
        );

        $account->addAvailability($this->availability1);

        $account->resetAvailabilities();

        $this->assertCount(0, $account->getAvailabilities());
    }

    
    public function testResetAvailabilitiesToList()
    {
        $account = new Account(
            $this->uuid,
            $this->firstname,
            $this->lastname,
            $this->email
        );

        $account->addAvailability($this->availability1);

        $account->resetAvailabilities(
            [
                $this->availability1,
                $this->availability3
            ]
        );

        $this->assertCount(2, $account->getAvailabilities());
        
        $this->assertEquals(
            [
                (string) $this->date1 => 1,
                (string) $this->date3 => 1
            ],
            $account->getAvailableDates()
        );

        
    }

    public function testCandidateAccount()
    {
        $account = new CandidateAccount(
            $this->uuid,
            $this->firstname,
            $this->lastname,
            $this->email
        );

        $this->assertEquals(
            $account->toArray(), 
            [
                'uuid'      => $this->stringUuid,
                'firstname' => $this->firstname,
                'lastname'  => $this->lastname,
                'email'     => $this->emailString,
                'type'      => 0
            ]
        );
    }

    public function testInterviewerAccount()
    {
        $account = new InterviewerAccount(
            $this->uuid,
            $this->firstname,
            $this->lastname,
            $this->email
        );

        $this->assertEquals(
            $account->toArray(), 
            [
                'uuid'      => $this->stringUuid,
                'firstname' => $this->firstname,
                'lastname'  => $this->lastname,
                'email'     => $this->emailString,
                'type'      => 1
            ]
        );
    }
}