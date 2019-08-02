<?php declare(strict_types=1);

namespace InterviewCalendar\Database;

use InterviewCalendar\Model\CandidateAccount;
use InterviewCalendar\Model\InterviewerAccount;
use InterviewCalendar\Model\Availability;
use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\Interval;
use InterviewCalendar\ValueObject\DateInFuture;
use InterviewCalendar\ValueObject\EmailAddress;
use InterviewCalendar\ValueObject\Exception;

class Repository
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAccount(Uuid $uuid): AccountInterface
    {
        $sth = $this->db->prepare("SELECT * FROM account where uuid = ?");
        $sth->execute([$uuid]);
        $account = $sth->fetch();

        if (empty($account)){
            throw Exception\InvalidAccount('Account not found', 404);
        }

        return $this->getAvailabilityOfAccount($this->toAccountObject($account));
    }

    public function getAccounts()
    {
        $sth = $this->db->prepare("SELECT * FROM account");
        $sth->execute();
        $accounts = $sth->fetchAll();

        $accountsArray = [];

        foreach($accounts as $account){
            array_push($accountsArray, $this->toAccountObject($account));
        }
  
        return $accountsArray;
    }

    public function addAccount(AccountInterface $account)
    {
        $sth = $this->db->prepare("INSERT INTO account (uuid, firstname, lastname, email, type)
                                   VALUES(:uuid, :firstname, :lastname, :email, :type)");
                                   
        $sth->execute($account->toArray());
       
    }
    
    public function getAvailabilitiesByAccountUuid(string $uuid): AccountInterface
    {
        $account = $this->getAccount(new Uuid($uuid));

        return $this->getAvailabilityOfAccount($account);
    }

    public function getAvailabilityOfAccount(AccountInterface $account): AccountInterface
    {
        $sth = $this->db->prepare(
            "SELECT date, json_agg(array[lower(interval), upper(interval)]) as intervals 
            FROM availability 
            WHERE account_uuid = ? AND date > CURRENT_DATE
            GROUP BY date"
        );
        $sth->execute([(string) $account->uuid()]);
        $availabilityRecords = $sth->fetchAll();
        
        foreach($availabilityRecords as $availability){
            $availableDate = $this->toAvailabilityObject(new DateInFuture($availability['date']));
            $intervals = json_decode($availability['intervals']);

            foreach($intervals as $interval){
                $availableDate->addInterval($this->toInterval($interval));
            }

            $account->addAvailability($availableDate);
        }

        return $account;
    }

    public function getAvailabilityOfAccounts(array $accountUuids)
    {
        $accountsUuidString = "'" . implode("','", $accountUuids) . "'";
        
        $sth = $this->db->prepare(
            "SELECT a.uuid, a.firstname, a.lastname, a.email, a.type, av.date, 
            json_agg(
               array[lower(av.interval), upper(av.interval) ]) as intervals
            FROM account a 
            LEFT JOIN availability av ON av.account_uuid = a.uuid
            WHERE av.account_uuid IN ($accountsUuidString)
            GROUP BY a.uuid, a.firstname, a.lastname, a.email, a.type, av.date, date
            "
        );
        $sth->execute();
        $availabilities = $sth->fetchAll();
        
        $accounts = [];
        foreach($availabilities as $availabilityRecord){
            if (!array_key_exists($availabilityRecord['uuid'], $accounts)){
                $account = $this->toAccountObject($availabilityRecord);

                $accounts[$availabilityRecord['uuid']] = $account;
            }
            
            $availability = new Availability(new DateInFuture($availabilityRecord['date']));
    
            $intervals = json_decode($availabilityRecord['intervals']);
            
            foreach($intervals as $interval){
                $intervalObject = $this->toInterval($interval);
                $availability->addInterval($intervalObject);

            }

            $accounts[$availabilityRecord['uuid']]->addAvailability($availability);
            
        }
        
        $availabilityRanges = [];
        $counter = 0;
        foreach($accounts as $account){
            $accountDates = [];
            foreach($account->getAvailabilities() as $availability){
                
                $range = $availability->range();
                if ($counter === 0){
                    $availabilityRanges[$range['date']] = $range['range'];
                }
                else{
                    $date = (string) $availability->date();
                    $accountDates[$date] = 1;
                    if (array_key_exists($date, $availabilityRanges)){
                        $availabilityRanges[$date] = array_intersect(
                            $range['range'], 
                            $availabilityRanges[$date]);
                    }
                    else{
                        unset($availabilityRanges[$date]);
                    }
                    
                }
                
            }

            if ($counter > 0){
                $availabilityRanges = array_intersect_key($availabilityRanges, $accountDates); 
            }
            
            $counter++;
        }

        return $availabilityRanges;
    }
    

    public function addAvailability(AccountInterface $account, Availability $availability, Interval $interval)
    {
       
        $sth = $this->db->prepare("INSERT INTO availability (account_uuid, date, interval) 
                    VALUES (:account_uuid, :date, :interval)");
        $sth->execute(
            [
                (string) $account->uuid(),
                (string) $availability->date(),
                (string) $interval
            ]
        );

    }

    public function setAvailabilities(AccountInterface $account, array $intervals): AccountInterface
    {
        
        if (gettype($intervals) !== 'array' || sizeOf($intervals) === 0){
            throw new InvalidUserInput('You have to submit at least one availability', 400);
        }
        
        $this->db->beginTransaction();

        $availabilities = [];
        
        try{
            foreach($intervals as $interval){
                if (!array_key_exists($interval['date'], $availabilities)){
                    $availability = new Availability(new DateInFuture($interval['date']));
                    $account->addAvailability($availability);
                    $availabilities[$interval['date']] = $availability;
                }
                
                $intervalObject = $this->toInterval([(int) $interval['start'], (int) $interval['end']]);
                $availability->addInterval($intervalObject);
                
                $this->addAvailability($account, $availability, $intervalObject);

            }

            $this->db->commit();
           
            return $this->getAvailabilityOfAccount($account);
        }
        catch(Exception $ex){
            $this->db->rollBack();
            throw new Exception\DatabaseException('An error occurred while executing query', 500);
        }

    }

    private function toAccountObject(array $account): AccountInterface
    {
        if ($account['type'] === 1){
            return new InterviewerAccount(
                new Uuid($account['uuid']), 
                $account['firstname'],
                $account['lastname'],
                new EmailAddress($account['email'])
            );
        }
        
        return new CandidateAccount(
            new Uuid($account['uuid']), 
            $account['firstname'],
            $account['lastname'],
            new EmailAddress($account['email'])
        );
    }

    private function toAvailabilityObject(DateInFuture $date): Availability
    {
        return new Availability($date);
    }

    private function toInterval(array $interval): Interval
    {
        return new Interval(
            $interval[0], 
            $interval[1], 
            $this->intervalStep);
    }


}