<?php

require __DIR__ . '/../vendor/autoload.php';

use App\PHPLoginManagement\Controller\HomeController;
use App\PHPLoginManagement\Core\Router;




Router::add(method: 'GET', path: '/', controller: HomeController::class, function: 'index', middleware: []);

Router::run();
