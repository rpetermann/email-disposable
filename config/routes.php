<?php

use Hyperf\HttpServer\Router\Router;

Router::addGroup('/v1/email-disposable/', function (){
    Router::get('check', 'App\Controller\CheckEmailDisposableController@check');
});
