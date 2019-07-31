<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'pgsql',
            'host' => 'postgres',
            'port' => '5432',
            'database' => 'interview_calendar',
            'username' => 'admin',
            'password' => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
    'parameters' => [
        'intervalStep' => 1
    ]
];