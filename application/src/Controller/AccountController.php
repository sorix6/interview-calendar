<?php declare(strict_types=1);

namespace InterviewCalendar\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;



use InterviewCalendar\Database\Repository;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\EmailAddress;
use InterviewCalendar\ValueObject\Exception\InvalidUuid;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;
use InterviewCalendar\Model\CandidateAccount;
use InterviewCalendar\Model\InterviewerAccount;

class AccountController
{
    private $query;
    private $intervalStep;

    public function __construct($db, int $intervalStep){
        $this->query = new Repository($db);
        $this->intervalStep = $intervalStep;
    }

    public function get(Request $request, Response $response, array $args)
    {
        $account = $this->query->getAccount(new Uuid($args['account_uuid']));
            
        return $response->withJson($account);
    }

    public function getAll(Request $request, Response $response, array $args)
    {
        $accounts = $this->query->getAccounts();
            
        return $response->withJson($accounts);
    }

    public function post(Request $request, Response $response, array $args)
    {   
        $input = $request->getParsedBody();
        
        if ($input['type'] != '0' && $input['type'] != '1'){
            throw new InvalidUserInput('Type is required and must be 0 or 1', 400);
        }
        else if (empty($input['firstname']) || empty($input['lastname'])){
            throw new InvalidUserInput('Firstname and lastname are required', 400);
        }
        else if (empty($input['email'])){
            throw new InvalidUserInput('Email is required', 400);
        }
        
        if ($input['type'] === 1){
            $account = new InterviewerAccount(
                new Uuid($input['uuid']),
                $input['firstname'],
                $input['lastname'],
                new EmailAddress($input['email'])
            );
        }
        else{
            $account = new CandidateAccount(
                new Uuid(),
                $input['firstname'],
                $input['lastname'],
                new EmailAddress($input['email'])
            );
        }

        $this->query->addAccount($account);

        return $response->withJson($account);
        
    }
}