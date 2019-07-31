<?php declare(strict_types=1);

namespace InterviewCalendar\Database;

use Ramsey\Uuid\Uuid;

use InterviewCalendar\Model\CandidateAccount;
use InterviewCalendar\Model\InterviewerAccount;
use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\EmailAddress;
use InterviewCalendar\ValueObject\Exception;

class Query
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAccount(string $uuid): AccountInterface
    {
        $sth = $this->db->prepare("SELECT * FROM account where uuid = ?");
        $sth->execute(array($uuid));
        $account = $sth->fetch();

        if (empty($account)){
            throw Exception\InvalidAccount('Account not found', 404);
        }

        return $this->toAccountObject($account);
    }

    public function getAccounts()
    {
        $sth = $this->db->prepare("SELECT * FROM account");
        $sth->execute();
        $accounts = $sth->fetchAll();

        $accountsArray = array();

        foreach($accounts as $account){
            array_push($accountsArray, $this->toAccountObject($account));
        }
  
        return $accountsArray;
    }

    public function addAccount(AccountInterface $account)
    {
        $sth = $this->db->prepare("INSERT INTO account (firstname, lastname, email)
                                   VALUES(:account, :lastname, :email)");
        $sth->execute($account->toArray());
       
    }

    public function getAvailabilityOfAccount(string $uuid)
    {
        $sth = $this->db->prepare("SELECT * FROM availability WHERE account_uuid = ? AND available_date > CURRENT_DATE");
        $sth->execute(array($uuid));
        $availability = $sth->fetchAll();

        return $availability;
    }

    public function getAvailabilityOfAccounts(array $accountUuids)
    {
        $accountUuidsString = "'" . implode("', '", $accountUuids) . "'";
        $sth = $this->db->prepare("SELECT * FROM availability WHERE account_uuid IN (?)");
        $sth->execute(array($accountUuidsString));
        $availability = $sth->fetchAll();

        return $availability;
    }

    public function setAvailability(Availability $availability)
    {
        $sth = $this->db->prepare("INSERT INTO availability (account_uuid, available_date, interval)
                                   VALUES(:account_uuid, :available_date, :interval)");
        $sth->execute(array(
                        'account_uuid' => $availability->accountUuid(),
                        'available_date' => $availability->date(),
                        'interval' => sprintf('[%s,%s]', $availability->intervalStart(), $availability->intervalEnd())
                    ));

    }

    private function toAccountObject($account)
    {
        if ($account['type'] === 1){
            return new InterviewerAccount(
                Uuid::fromString($account['uuid']), 
                $account['firstname'],
                $account['lastname'],
                new EmailAddress($account['email'])
            );
        }
        
        return new CandidateAccount(
            Uuid::fromString($account['uuid']), 
            $account['firstname'],
            $account['lastname'],
            new EmailAddress($account['email'])
        );
    }


}