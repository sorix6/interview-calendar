<?php declare(strict_types=1);

namespace InterviewCalendar\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

use InterviewCalendar\Handler\AccountHandler;
use InterviewCalendar\Database\Repository;

class AccountController
{
    private $handler;
    private $intervalStep;

    public function __construct($db, int $intervalStep){
        $this->handler = new AccountHandler(new Repository($db));
        $this->intervalStep = $intervalStep;
    }

    public function get(Request $request, Response $response, array $args)
    {
        return $response->withJson($this->handler->getAccount($args));
    }

    public function getAll(Request $request, Response $response, array $args)
    {           
        return $response->withJson($this->handler->getAccounts());
    }

    public function post(Request $request, Response $response, array $args)
    {   
        return $response->withJson(
            $this->handler->addAccount($request)
        );
        
    }
}