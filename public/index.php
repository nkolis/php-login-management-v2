<?php
date_default_timezone_set("Asia/Jakarta");
require __DIR__ . '/../vendor/autoload.php';

use App\PHPLoginManagement\Controller\HomeController;
use App\PHPLoginManagement\Controller\UserController;
use App\PHPLoginManagement\Core\Router;
use App\PHPLoginManagement\Middleware\MustLoginMiddleware;
use App\PHPLoginManagement\Middleware\MustNotLoginMiddleware;

// Home
Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: [MustNotLoginMiddleware::class]);

// User Register
Router::add(method: 'GET', path: '/users/register', controller: UserController::class, function: 'register', middleware: [MustNotLoginMiddleware::class]);
Router::add(method: 'POST', path: '/users/register', controller: UserController::class, function: 'postRegister', middleware: [MustNotLoginMiddleware::class]);

// User Login
Router::add(method: 'GET', path: '/users/dashboard', controller: UserController::class, function: 'dashboard', middleware: [MustLoginMiddleware::class]);
Router::add(method: 'GET', path: '/users/login', controller: UserController::class, function: 'login', middleware: [MustNotLoginMiddleware::class]);
Router::add(method: 'POST', path: '/users/login', controller: UserController::class, function: 'postLogin', middleware: [MustNotLoginMiddleware::class]);

// User Profile
Router::add(method: 'GET', path: '/users/profile', controller: UserController::class, function: 'profile', middleware: [MustLoginMiddleware::class]);
Router::add(method: 'POST', path: '/users/profile', controller: UserController::class, function: 'postUpdateProfile', middleware: [MustLoginMiddleware::class]);

// User Password
Router::add(method: 'GET', path: '/users/password', controller: UserController::class, function: 'password', middleware: [MustLoginMiddleware::class]);
Router::add(method: 'POST', path: '/users/password', controller: UserController::class, function: 'postUpdatePassword', middleware: [MustLoginMiddleware::class]);

// User verification
Router::add(method: 'GET', path: '/users/verification', controller: UserController::class, function: 'verification', middleware: [MustLoginMiddleware::class]);
Router::add(method: 'POST', path: '/users/verification', controller: UserController::class, function: 'postVerification', middleware: [MustLoginMiddleware::class]);
Router::add(method: 'POST', path: '/users/verification/sendcode', controller: UserController::class, function: 'postSendcode', middleware: [MustLoginMiddleware::class]);

// User logout
Router::add(method: 'GET', path: '/users/logout', controller: UserController::class, function: 'logout', middleware: [MustLoginMiddleware::class]);

Router::run();
