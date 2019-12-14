<?php

use InterviewCalendar\Tests\BaseTestCase;

use InterviewCalendar\Database\Repository;
use InterviewCalendar\Model\CandidateAccount;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\Exception\InvalidAccount;


class RepositoryTest extends BaseTestCase
{

    public function testGetAccount()
    {
        $uuid = $this->getMockBuilder(Uuid::class)          
                     ->disableOriginalConstructor()
                     ->getMock();

        $uuid->expects($this->any())
             ->method('__toString')
             ->will($this->returnValue(
                '3d869012-c8df-4f41-b866-4b813b38a4e8'
            ));

        $account = $this->repository->getAccount($uuid);

        $this->assertEquals((string) $account->uuid(), (string) $uuid);
    }

    public function testInexistentAccountUuidThrowsException()
    {
        $this->expectException(InvalidAccount::class);

        $uuid = $this->getMockBuilder(Uuid::class)          
                     ->disableOriginalConstructor()
                     ->getMock();

        $uuid->expects($this->any())
             ->method('__toString')
             ->will($this->returnValue(
                '3d869012-c8df-4f41-b866-4b813b38a4e9'
            ));

        $this->repository->getAccount($uuid);

    }

    public function testGetAccounts()
    {
        $accounts = $this->repository->getAccounts();

        $this->assertCount(8, $accounts);
    }

    public function testAddCandidateAccount()
    {
        $account = $this->repository->addAccount(
            [
                'type'      => 0,
                'firstname' => 'Daisy',
                'lastname'  => 'Duck',
                'email'     => 'dduck@yahoo.com'
            ]
        );

        $this->assertEquals($account->firstname(), 'Daisy');
        $this->assertEquals($account->lastname(), 'Duck');
        $this->assertEquals((string) $account->email(), 'dduck@yahoo.com');
        $this->assertInstanceOf(CandidateAccount::class, $account);

        $account = $this->repository->getAccount($account->uuid());

        $this->addToAssertionCount(1);
    }
   
}