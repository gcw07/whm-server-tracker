<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WHM Server Settings
    |--------------------------------------------------------------------------
    */

    'whm' => [
        /**
         * The protocol for the WHM servers your connecting too.
         */
        'protocol' => 'https',

        /**
         * This is the server username the API token was created under. Usually
         * this username represents the root user or a reseller user.
         */
        'username' => 'root',

        /**
         * The connection timeout in seconds.
         */
        'connection_timeout' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    'notifications' => [

        'notifications' => [

        ],

        /**
         * Send failed notification only when it fails after every given number of minutes.
         */
        'resend_failed_notification_every_minutes' => 1440,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore Usernames
    |--------------------------------------------------------------------------
    | Skip over usernames that are ignored.
    */

    'ignore_usernames' => [
        'gwscripts',
    ],

    /*
    |--------------------------------------------------------------------------
    | Valid Administrator Email Addresses
    |--------------------------------------------------------------------------
    | These are valid email addresses who have access to the Horizon and
    | WebSockets dashboards.
    */

    'admin_emails' => [
        'grant@gwscripts.com',
    ],

];
