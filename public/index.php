<?php

require __DIR__ . '/../vendor/autoload.php';

use App\PHPLoginManagement\Controller\HomeController;
use App\PHPLoginManagement\Controller\UserController;
use App\PHPLoginManagement\Core\Router;



// Home
Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: []);

// User Register
Router::add(method: 'GET', path: '/users/register', controller: UserController::class, function: 'register', middleware: []);
Router::add(method: 'POST', path: '/users/register', controller: UserController::class, function: 'postRegister', middleware: []);


Router::run();
