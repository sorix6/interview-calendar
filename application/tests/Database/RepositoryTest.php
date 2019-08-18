<?php

use PHPUnit\Framework\TestCase;

use InterviewCalendar\Database\Repository;
use InterviewCalendar\Model\CandidateAccount;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\Exception\InvalidAccount;



class RepositoryTest extends TestCase
{
    public static $pdo;
    public static $repository;

    public static function setUpBeforeClass(): void
    {
        self::createTestDatabase();

        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                'postgres', 
                '5432', 
                'interview_calendar_test',
                'admin', 
                'password'
            );

        self::$pdo = new PDO($conStr);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        self::populateDatabase();

        self::$repository = new Repository(self::$pdo);
    }

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

        $account = self::$repository->getAccount($uuid);

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

        self::$repository->getAccount($uuid);

    }

    public function testGetAccounts()
    {
        $accounts = self::$repository->getAccounts();

        $this->assertCount(8, $accounts);
    }

    public function testAddCandidateAccount()
    {
        $account = self::$repository->addAccount(
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

        $account = self::$repository->getAccount($account->uuid());

        $this->addToAssertionCount(1);
    }

    public static function createTestDatabase()
    {
        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                'postgres', 
                '5432', 
                'postgres',
                'admin', 
                'password'
            );

        $pdo = new PDO($conStr);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        $sth = $pdo->prepare('CREATE DATABASE interview_calendar_test');
        $sth->execute();

        $pdo = null;
    }

    public static function populateDatabase()
    {
        $sql = 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";';
        $sth = self::$pdo->prepare($sql);
        $sth->execute();

        $sql = "CREATE TABLE account (
                uuid UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
                firstname VARCHAR(100) NOT NULL,
                lastname  VARCHAR(100) NOT NULL,
                email     VARCHAR(255) UNIQUE NOT NULL,
                type      INT NOT NULL DEFAULT 0,
                created   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated   TIMESTAMP
            );";

        $sth = self::$pdo->prepare($sql);
        $sth->execute();

        $sql = "CREATE TABLE availability (
                id serial PRIMARY KEY,
                account_uuid UUID NOT NULL REFERENCES account(uuid),
                date DATE NOT NULL,
                interval INT4RANGE NOT NULL,
                created   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated   TIMESTAMP,
                UNIQUE(account_uuid, date, interval)
            );";

        $sth = self::$pdo->prepare($sql);
        $sth->execute();


        $sql = "INSERT INTO account (uuid, firstname, lastname, email, type, created, updated) VALUES
            ('3d869012-c8df-4f41-b866-4b813b38a4e8',	'Ines',	'Doe',	'idoe@gmail.com',	1,	'2019-08-01 21:20:46.142243',	NULL),
            ('a68d870c-4a06-4138-afaa-e0c77b64d230',	'Ingrid',	'Smith',	'ismith@gmail.com',	1,	'2019-08-01 21:20:46.142243',	NULL),
            ('26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'Carl',	'White',	'cwhite@gmail.com',	'0',	'2019-08-01 21:20:46.142243',	NULL),
            ('c497ae30-3461-4eea-8831-ae41cafb461a',	'Teddy',	'Smith',	'tsmith@yahoo.com',	'0',	'2019-08-01 21:46:32.832275',	NULL),
            ('f11770f6-881f-490f-b09c-abb05e60dc6f',	'Tina',	'Jonathan',	'tjohnathan@yahoo.com',	'0',	'2019-08-02 19:40:08.620367',	NULL),
            ('8cff2d2c-6335-4d0a-8d2a-722f2dd347ea',	'jessie',	'Jon',	'jjon@yahoo.com',	'0',	'2019-08-02 21:56:58.889215',	NULL),
            ('1df2090e-2f69-4243-a915-982cf75aa899',	'Danny',	'Black',	'dannyb@yahoo.com',	1,	'2019-08-03 11:20:35.255452',	NULL),
            ('a271e346-d818-4960-b9f1-902144b97c5d',	'Debra',	'Black',	'dblack@yahoo.com',	'0',	'2019-08-18 14:03:11.71004',	NULL);
            ";

        $sth = self::$pdo->prepare($sql);
        $sth->execute();

        $sql = "INSERT INTO availability (id, account_uuid, date, interval, created, updated) VALUES
            (1,	'3d869012-c8df-4f41-b866-4b813b38a4e8',	'2019-09-03',	'[10,16)',	'2019-08-01 22:06:35.378466',	NULL),
            (2,	'3d869012-c8df-4f41-b866-4b813b38a4e8',	'2019-09-02',	'[10,20)',	'2019-08-01 22:06:35.378466',	NULL),
            (3,	'a68d870c-4a06-4138-afaa-e0c77b64d230',	'2019-09-12',	'[10,16)',	'2019-08-01 22:07:18.266947',	NULL),
            (4,	'a68d870c-4a06-4138-afaa-e0c77b64d230',	'2019-09-02',	'[15,20)',	'2019-08-01 22:07:18.266947',	NULL),
            (5,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-09-12',	'[10,12)',	'2019-08-01 22:10:15.118125',	NULL),
            (6,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-09-03',	'[9,12)',	'2019-08-01 22:10:15.118125',	NULL),
            (8,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-09-02',	'[17,22)',	'2019-08-01 22:10:50.459881',	NULL),
            (9,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-09-02',	'[5,12)',	'2019-08-01 22:57:26.387165',	NULL),
            (10,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-09-22',	'[5,11)',	'2019-08-02 02:20:16.587037',	NULL),
            (11,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-11',	'[9,18)',	'2019-08-02 20:19:23.881308',	NULL),
            (12,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-11',	'[19,21)',	'2019-08-02 20:19:23.881308',	NULL),
            (13,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-10',	'[9,18)',	'2019-08-02 20:21:12.943836',	NULL),
            (14,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-10',	'[19,21)',	'2019-08-02 20:21:12.943836',	NULL),
            (15,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-15',	'[9,18)',	'2019-08-02 20:23:34.397644',	NULL),
            (16,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-15',	'[19,21)',	'2019-08-02 20:23:34.397644',	NULL),
            (17,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-16',	'[9,18)',	'2019-08-02 20:23:48.308293',	NULL),
            (18,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-16',	'[19,21)',	'2019-08-02 20:23:48.308293',	NULL),
            (19,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-17',	'[9,18)',	'2019-08-02 20:29:23.590904',	NULL),
            (20,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-17',	'[19,21)',	'2019-08-02 20:29:23.590904',	NULL),
            (21,	'26f852c1-1b12-4b78-a6dd-3a69de7eaa4d',	'2019-10-18',	'[9,18)',	'2019-08-02 20:30:06.419098',	NULL)
        ;";
    
        $sth = self::$pdo->prepare($sql);
        $sth->execute();
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo = null;
        self::$repository = null;

        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                'postgres', 
                '5432', 
                'postgres',
                'admin', 
                'password'
            );

        $pdo = new PDO($conStr);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        $sth = $pdo->prepare('DROP DATABASE interview_calendar_test');
        $sth->execute();

        $pdo = null;
    }
    
}