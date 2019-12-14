<?php

    namespace InterviewCalendar\Controller;

    use Slim\App;

    use InterviewCalendar\Database\Query;
    


    return function (App $app) {
        
        $app->post('/accounts/availability', 'InterviewCalendar\Controller\AvailabilityController:getMultiple');
        
        $app->post('/account/{account_uuid}/availability', 'InterviewCalendar\Controller\AvailabilityController:post');

        $app->get('/account', 'InterviewCalendar\Controller\AccountController:getAll');

        $app->get('/account/{account_uuid}', 'InterviewCalendar\Controller\AccountController:get');

        $app->post('/account', 'InterviewCalendar\Controller\AccountController:post');
       
    };