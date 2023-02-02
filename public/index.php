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

// User Login
Router::add(method: 'GET', path: '/users/dashboard', controller: UserController::class, function: 'dashboard', middleware: []);
Router::add(method: 'GET', path: '/users/login', controller: UserController::class, function: 'login', middleware: []);
Router::add(method: 'POST', path: '/users/login', controller: UserController::class, function: 'postLogin', middleware: []);

Router::run();
