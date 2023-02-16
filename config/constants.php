<?php

const DEFAULT_COOKIE_EXPIRES = 60 * 60 * 24 * 7;
const BASE_URL = "http://localhost/php-login-management-v2/public";
const DATABASE_CONFIG = [
  'database' => [
    'test' => [
      'username' => 'root',
      'password' => '',
      'host' => 'localhost',
      'dbname' => 'php_login_management_v2_test'
    ],
    'prod' => [
      'test' => [
        'username' => 'root',
        'password' => '',
        'host' => 'localhost',
        'dbname' => 'php_login_management_v2'
      ]
    ]
  ]
];
