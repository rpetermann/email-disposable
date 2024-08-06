<?php

use App\Exception\Handler\AppExceptionHandler;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;

return [
    'handler' => [
        'http' => [
            HttpExceptionHandler::class,
            AppExceptionHandler::class,
        ],
    ],
];
