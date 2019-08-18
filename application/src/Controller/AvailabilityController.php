<?php declare(strict_types=1);

namespace InterviewCalendar\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

use InterviewCalendar\Handler\AvailabilityHandler;
use InterviewCalendar\Database\Repository;

class AvailabilityController
{
    private $handler;

    public function __construct($db){
        $this->handler = new AvailabilityHandler(new Repository($db));
    }

    public function get(Request $request, Response $response, array $args)
    { 
        return $response->withJson(
            $this->handler->getAccountAvailabilities($args)
        );
    }

    public function getMultiple(Request $request, Response $response, array $args)
    {
        return $response->withJson(
            $this->handler->getCommonAvailabilityForMultipleAccounts($request)
        );
    }

    public function post(Request $request, Response $response, array $args)
    {
        return $response->withJson(
            $this->handler->addAvailabilitiesToAccount(
                $request, $args
            )
        );
       
    }
}