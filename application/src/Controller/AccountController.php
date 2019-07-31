<?php declare(strict_types=1);

namespace InterviewCalendar\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;
use Ramsey\Uuid\Uuid;


use InterviewCalendar\Database\Query;
use InterviewCalendar\ValueObject\Exception\InvalidUuid;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;

class AccountController
{
    private $query;
    private $intervalStep;

    public function __construct($db, int $intervalStep){
        $this->query = new Query($db);
        $this->intervalStep = $intervalStep;
    }

    public function get(Request $request, Response $response, array $args)
    {
        $account = $this->query->getAccount($args['account_uuid']);
            
        return $response->withJson($account);
    }

    public function getAll(Request $request, Response $response, array $args)
    {
        $accounts = $this->query->getAccounts();
            
        return $response->withJson($accounts);
    }

    public function post(Request $request, Response $response, array $args)
    {
        $accountUuid = $args['account_uuid'];
            
        if (!Uuid::isValid($accountUuid) || Uuid::fromString($accountUuid)->getVersion() != '4'){
            throw new InvalidUuid('Invalid user UUID', 400);
        }

        $input = $request->getParsedBody();

        if (gettype($input['interval']) !== 'array' || sizeOf($input['interval']) === 0){
            throw new InvalidUserInput('The time interval cannot be empty');
        }
        else if (empty($input['available_date'])){
            throw new InvalidUserInput('Invalid date', 400);
        }

        foreach($input['interval'] as $inputInterval){
            $intervalObject = new Interval($inputInterval['start'], $inputInterval['end'], $intervalStep);
            $date = new DateInFuture($inputInterval['available_date']);
            
            $accountData = $this->query->getAccount($accountUuid);
            
            if (empty($accountData)){

            }

            $sql = "INSERT INTO availability (account_uuid, available_date, interval) 
                VALUES (:account_uuid, :available_date, :interval)";
            $sth->execute(array(
                'account_uuid' => $accountUuid, 
                'available_date' => $availableDate, 
                'interval' => $interval)
            );

        }
    }
}