<?php

namespace InterviewCalendar\Tests;

use InterviewCalendar\Database\Repository;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use PDO;

abstract class BaseTestCase extends TestCase
{

    protected $app;
    protected $withMiddleware = true;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();

        // connect to database
        $pdo = $this->connectToDatabase();
        $this->repository = new Repository($pdo);
    }

    protected function connectToDatabase()
    {
        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                'postgres', 
                '5432', 
                'interview_calendar_test',
                'admin', 
                'password'
            );

        $pdo = new PDO($conStr);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }


    protected function tearDown(): void
    {
        unset($this->app);
        parent::tearDown();
    }

    /**
     * Process the application given a request method and URI
     *
     * @param string            $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string            $requestUri    the request URI
     * @param array|object|null $requestData   the request data
     *
     * @param array             $headers
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function runApp($requestMethod, $requestUri, $requestData = null, $headers = [])
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            array_merge(
                [
                    'REQUEST_METHOD'   => $requestMethod,
                    'REQUEST_URI'      => $requestUri,
                    'Content-Type'     => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
                $headers
            )
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();

        // Process the application and Return the response
        return $this->app->process($request, $response);
    }

    /**
     * Make a request to the Api
     *
     * @param       $requestMethod
     * @param       $requestUri
     * @param null  $requestData
     * @param array $headers
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     */
    public function request($requestMethod, $requestUri, $requestData = null, $headers = [])
    {
        return $this->runApp($requestMethod, $requestUri, $requestData, $headers);
    }

    protected function createApplication()
    {
        require __DIR__ . '/../vendor/autoload.php';

        // Use the application settings
        $settings = require __DIR__ . '/config/settings.php';

        // Instantiate the application
        $this->app = $app = new App($settings);

        // Set up dependencies
        $dependencies = require __DIR__ . '/../src/config/dependencies.php';
        $dependencies($app);


        // Register routes
        $routes = require __DIR__ . '/../src/Controller/MainController.php';
        $routes($app);

        $app->getContainer()->get('db');
        // Run app

        $app->run();
    }
}