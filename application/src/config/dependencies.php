<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // Service factory for the ORM
    $container['db'] = function ($container) {
        $settings = $container->get('settings')['db'];
        
        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                $settings['host'], 
                $settings['port'], 
                $settings['database'], 
                $settings['username'], 
                $settings['password']);

        $pdo = new PDO($conStr);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    };

    $container['InterviewCalendar\Controller\AvailabilityController'] = function ($c) {
        return new \InterviewCalendar\Controller\AvailabilityController($c['db']);
    };

    $container['InterviewCalendar\Controller\AccountController'] = function ($c) {
        return new \InterviewCalendar\Controller\AccountController($c['db'], $c['parameters']['intervalStep']);
    };

};