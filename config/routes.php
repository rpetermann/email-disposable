<?php

use Hyperf\HttpServer\Router\Router;

Router::addGroup('/v1/email-disposable/', function (): void {
    Router::get('check', 'App\Controller\CheckEmailDisposableController@check');
});
