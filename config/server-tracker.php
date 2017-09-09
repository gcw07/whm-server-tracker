<?php

return [

    /*
    |--------------------------------------------------------------------------
    | URL Defaults
    |--------------------------------------------------------------------------
    */

    'urls' => [
        'protocol' => 'https',
        'api-path' => 'json-api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Remote Server Defaults
    |--------------------------------------------------------------------------
    | This is the server username and the number of seconds before timing out
    | on server requests. The timeout is in seconds.
    */

    'remote' => [
        'username' => 'root',
        'timeout' => 10
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Usernames
    |--------------------------------------------------------------------------
    | Skip over usernames that are ignored.
    */

    'ignore_usernames' => [
        'gwscripts'
    ]

];
