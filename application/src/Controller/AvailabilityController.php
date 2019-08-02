<?php declare(strict_types=1);

namespace InterviewCalendar\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;

use InterviewCalendar\Database\Repository;
use InterviewCalendar\ValueObject\Exception\InvalidUuid;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;
use InterviewCalendar\ValueObject\Interval;
use InterviewCalendar\ValueObject\DateInFuture;
use InterviewCalendar\ValueObject\Uuid;

class AvailabilityController
{
    private $query;

    public function __construct($db){
        $this->query = new Repository($db);
    }

    public function get(Request $request, Response $response, array $args)
    {
        $availability = $this->query->getAvailabilitiesByAccountUuid($args['account_uuid']);
            
        return $response->withJson($availability);
    }

    public function getAll(Request $request, Response $response, array $args)
    {
        $input = $request->getParsedBody();

        if (empty($input['accounts_uuid'])){
            throw new InvalidUserInput('You must select at least one account', 400);
        };

        $availabilities = $this->query->getAvailabilityOfAccounts($input['accounts_uuid']);
            
        return $response->withJson($availabilities);
    }

    public function post(Request $request, Response $response, array $args)
    {
        $input = $request->getParsedBody();
        $account = $this->query->getAccount(new Uuid($args['account_uuid']));

        $availabilities = $this->query->setAvailabilities($account, $input['intervals']);

        return $response->withJson($availabilities);
       
    }
}