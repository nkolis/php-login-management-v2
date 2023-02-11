<?php
function getConfig()
{
  return [
    'baseurl' => 'http://localhost/php-login-management-v2/public',
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
}
