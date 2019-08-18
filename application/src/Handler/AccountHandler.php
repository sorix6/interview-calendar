<?php 

namespace InterviewCalendar\Handler;

use Slim\Http\Request;

use InterviewCalendar\Database\Repository;
use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;

class AccountHandler
{
    private $query;

    public function __construct(Repository $repository){
        $this->query = $repository;
    }

    public function getAccount(array $args): AccountInterface
    {
        if (empty($args['account_uuid'])) {
            throw new InvalidUserInput('The field account_uuid is required', 400);
        }

        return $this->query->getAccount(new Uuid($args['account_uuid']));
    }

    public function getAccounts(): array
    {
        return $this->query->getAccounts();
    }

    public function addAccount(Request $request): AccountInterface
    {
        $input = $request->getParsedBody();
        $this->validatePostPayload($input);

        return $this->query->addAccount($input);
    }

    private function validatePostPayload(array $input)
    {
        if (!in_array($input['type'], ['0', '1'])){
            throw new InvalidUserInput('Type is required and must be 0 or 1', 400);
        }
        else if (empty($input['firstname'])){
            throw new InvalidUserInput('Firstname is required', 400);
        }
        else if (empty($input['lastname'])){
            throw new InvalidUserInput('Lastname is required', 400);
        }
        else if (empty($input['email'])){
            throw new InvalidUserInput('Email is required', 400);
        }
    }

    
}