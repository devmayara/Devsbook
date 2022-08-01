<?php

use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin');
$router->post('/cadastro', 'LoginController@signup');
