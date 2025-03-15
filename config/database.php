<?php

use Illuminate\Support\Str;

return [

    'connections' => [
        'mysql_v1_db' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('WHM_V1_DB_HOST', '127.0.0.1'),
            'port' => env('WHM_V1_DB_PORT', '3306'),
            'database' => env('WHM_V1_DB_DATABASE', 'forge'),
            'username' => env('WHM_V1_DB_USERNAME', 'forge'),
            'password' => env('WHM_V1_DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],

    'encryption_key' => env('APP_ENCRYPTION_KEY'),

];
