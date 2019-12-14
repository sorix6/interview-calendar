<?php 

namespace InterviewCalendar\Handler;

use Slim\Http\Request;

use InterviewCalendar\Database\Repository;
use InterviewCalendar\Model\AccountInterface;
use InterviewCalendar\ValueObject\Uuid;
use InterviewCalendar\ValueObject\Exception\InvalidUserInput;

class AvailabilityHandler
{
    private $query;

    public function __construct(Repository $repository){
        $this->query = $repository;
    }

    public function getAccountAvailabilities(array $args): AccountInterface
    {
        if (empty($args['account_uuid'])) {
            throw new InvalidUserInput('The field account_uuid is required', 400);
        }

        return $this->query->getAvailabilitiesByAccountUuid($args['account_uuid']);
    
    }

    public function getCommonAvailabilityForMultipleAccounts(Request $request): array
    {
        $input = $request->getParsedBody();

        if (empty($input['accounts_uuid'])){
            throw new InvalidUserInput('You must select at least one account', 400);
        };

        return $this->query->getAvailabilityOfAccounts($input['accounts_uuid']);
            
    }

    public function addAvailabilitiesToAccount(Request $request, array $args): AccountInterface
    {
        $input = $request->getParsedBody();

        if (empty($input['intervals'])) {
            throw new InvalidUserInput('At least one time interval is required', 400);
        }

        return $this->query->setAvailabilities(
            $this->getAccount($args), 
            $input['intervals']
        );

    }

    private function getAccount(array $args): AccountInterface
    {
        if (empty($args['account_uuid'])) {
            throw new InvalidUserInput('The field account_uuid is required', 400);
        }

        return $this->query->getAccount(new Uuid($args['account_uuid']));
    }
}