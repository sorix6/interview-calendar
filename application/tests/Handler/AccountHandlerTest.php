<?php

use PHPUnit\Framework\TestCase;

use Slim\Http\Request;

use InterviewCalendar\Database\Repository;
use InterviewCalendar\Handler\AccountHandler;
use InterviewCalendar\Model\Account;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;
use InterviewCalendar\ValueObject\Exception\InvalidUuid;

class AccountHandlerTest extends TestCase
{
    
    public function setUp(): void
    {
        $this->query = $this->getMockBuilder(Repository::class)
                            ->setMethods([])                    
                            ->disableOriginalConstructor()
                            ->getMock();

        $this->handler = new AccountHandler($this->query);                           

    }

    public function testAccountIsRetrievedByValidUuid()
    {
        $this->query->expects($this->once())
                    ->method('getAccount');
        
        $this->handler->getAccount(
            [
                'account_uuid' => '2afbdaf5-2592-413a-8f58-6f4717844361'
            ]
        );
    }

    public function testMissingAccountUuidThrowsError()
    {
        $this->expectException(InvalidUserInput::class);
        
        $this->handler->getAccount([]);
    }

    public function testInvalidAccountUuidThrowsError()
    {
        $this->expectException(InvalidUuid::class);
        
        $this->handler->getAccount(
            [
                'account_uuid' => '552afbdaf5-2592-413a-8f58-6f4717844361'
            ]
        );
    }

    public function testGetAccounts()
    {
        $this->query->expects($this->once())
                    ->method('getAccounts');
        
        $this->handler->getAccounts();
    }

    public function testAddAccount()
    {
        $account = $this->getMockBuilder(Account::class)          
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->query->expects($this->any())
                    ->method('addAccount')
                    ->will($this->returnValue(
                        $account
                    ));

        $this->query->expects($this->once())
                    ->method('addAccount');

        $request =  $this->getMockBuilder(Request::class)
                         ->setMethods(['getParsedBody'])                    
                         ->disableOriginalConstructor()
                         ->getMock();

        $request->expects($this->any())
                ->method('getParsedBody')
                ->will($this->returnValue(
                    [
                        'firstname' => 'testFirstname',
                        'lastname' => 'testLastname',
                        'type' => '1',
                        'email' => 'test@test.com'
                    ]
                ));
        
        $this->handler->addAccount($request);

    }

    /**
     * @dataProvider invalidPayloadProvider
     */
    public function testInvalidPayloadThrowsException($payload)
    {
        $this->expectException(InvalidUserInput::class);

        $request =  $this->getMockBuilder(Request::class)
                         ->setMethods(['getParsedBody'])                    
                         ->disableOriginalConstructor()
                         ->getMock();

        $request->expects($this->any())
                ->method('getParsedBody')
                ->will($this->returnValue(
                    $payload
                ));
        
        $this->handler->addAccount($request);

    }

    public function invalidPayloadProvider()
    {
        return [
            [
                [
                    'firstname' => 'testFirstname',
                    'lastname' => 'testLastname',
                    'type' => '-1',
                    'email' => 'test@test.com'
                ]
            ],
            [
                [
                    'firstname' => 'testFirstname',
                    'lastname' => 'testLastname',
                    'email' => 'test@test.com'
                ]
            ],
            [
                [
                    'lastname' => 'testLastname',
                    'type' => '1',
                    'email' => 'test@test.com'
                ]
            ],
            [
                [
                    'firstname' => 'testFirstname',
                    'type' => '0',
                    'email' => 'test@test.com'
                ]
            ],
            [
                [
                    'firstname' => 'testFirstname',
                    'lastname' => 'testLastname',
                    'type' => '1'
                ]
            ]
        ];
    }

    
}