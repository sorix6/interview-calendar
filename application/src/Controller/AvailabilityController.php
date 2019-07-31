<?php declare(strict_types=1);

namespace InterviewCalendar\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;
use Ramsey\Uuid\Uuid;


use InterviewCalendar\Database\Query;
use InterviewCalendar\ValueObject\Exception\InvalidUuid;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;

class AvailabilityController
{
    private $query;

    public function __construct($db){
        $this->query = new Query($db);
    }

    public function get(Request $request, Response $response, array $args)
    {
        $availability = $this->query->getAvailabilityOfAccount($args['account_uuid']);
            
        return $response->withJson($availability);
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
            
            $sql = "INSERT INTO schedule (account_uuid, available_date, interval) 
                VALUES (:account_uuid, :available_date, :interval)";
            $sth->execute(array(
                'account_uuid' => $accountUuid, 
                'available_date' => $availableDate, 
                'interval' => $interval)
            );

        }
    }
}