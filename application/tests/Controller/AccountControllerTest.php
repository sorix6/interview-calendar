<?php

use InterviewCalendar\Tests\BaseTestCase;

class AccountControllerTest extends BaseTestCase
{
    
    public function testGetAccounts()
    {
        $response = $this->runApp('GET', '/account');

        $accounts = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(8, $accounts);

        return $accounts[0];
    }

    /**
     * @depends testGetAccounts
     */
    public function testGetAccount($account)
    {
        $uuid = $account->uuid;
        $response = $this->runApp('GET', '/account/' . $uuid);

        $account = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals((string) $uuid, (string) $account->uuid);

    }


   
    // public function testaddAccount()
    // {
    //     $response = $this->runApp('POST', '/account', ['test']);
    // }
}