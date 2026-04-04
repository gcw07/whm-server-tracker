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
            'fetched_data_succeeded' => [],
            'fetched_data_failed' => ['mail'],
        ],

        'mail' => [
            'to' => env('SERVER_TRACKER_MAIL_TO_ADDRESS'),
        ],

        'slack' => [
            'webhook_url' => env('SERVER_TRACKER_SLACK_WEBHOOK_URL'),
        ],

        /**
         * Send failed notification only when it fails after every given number of hours.
         */
        'resend_failed_notification_every_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Disk Usage Messages
    |--------------------------------------------------------------------------
    */

    'disk_usage' => [
        /**
         * The disk percentage of the server when it should show various states.
         */
        'server_disk_warning' => 80,
        'server_disk_critical' => 90,
        'server_disk_full' => 98,

        /**
         * The disk percentage of an account when it should show various states.
         */
        'account_disk_warning' => 80,
        'account_disk_critical' => 90,
        'account_disk_full' => 98,
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklist Settings
    |--------------------------------------------------------------------------
    | Configuration for the driver-based blacklist checking system.
    | cached_seconds controls how long resolved IPs and DNSBL results are
    | cached. Multiple domains on the same server share cached IP lookups.
    */

    'blacklist' => [
        'cached_seconds' => 7200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Lighthouse Audit Reports
    |--------------------------------------------------------------------------
    | Limit audits to run only after a certain amount of time.
    | The number of seconds for how long to run the audits for.
    */

    'lighthouse_audits' => [
        'run_audit_every_hours' => 160,
        'audit_timeout' => env('SERVER_TRACKER_LIGHTHOUSE_AUDIT_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Name Expiration RDAP Server
    |--------------------------------------------------------------------------
    | This is the RDAP server you are using to do domain name
    | expiration lookups.
    */

    'rdap_server' => 'rdap.org',

    /*
    |--------------------------------------------------------------------------
    | Domain Name Expiration Notification
    |--------------------------------------------------------------------------
    | This is the number of given days before a domain name expires when
    | to send a notification.
    */

    'domain_name_expires_within_days' => 14,

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
