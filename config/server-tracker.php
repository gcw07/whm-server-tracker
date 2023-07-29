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
    | Blacklist Servers
    |--------------------------------------------------------------------------
    | A list of blacklist servers to check each site against.
    */

    'blacklist_servers' => [
        'all.s5h.net',
        'b.barracudacentral.org',
        'bl.spamcop.net',
        'blacklist.woody.ch',
        'bogons.cymru.com',
        'cbl.abuseat.org',
        'db.wpbl.info',
        'dnsbl-1.uceprotect.net',
        'dnsbl-2.uceprotect.net',
        'dnsbl-3.uceprotect.net',
        'dnsbl.anticaptcha.net',
        'dnsbl.cyberlogic.net',
        'dnsbl.dronebl.org',
        'dnsbl.spfbl.net',
        'dul.dnsbl.sorbs.net',
        'dyna.spamrats.com',
        'http.dnsbl.sorbs.net',
        'ips.backscatterer.org',
        'ix.dnsbl.manitu.net',
        'korea.services.net',
        'misc.dnsbl.sorbs.net',
        'noptr.spamrats.com',
        'orvedb.aupads.org',
        'psbl.surriel.com',
        'relays.nether.net',
        'singular.ttk.pte.hu',
        'smtp.dnsbl.sorbs.net',
        'socks.dnsbl.sorbs.net',
        'spam.dnsbl.anonmails.de',
        'spam.dnsbl.sorbs.net',
        'spam.spamrats.com',
        'spambot.bls.digibase.ca',
        'spamrbl.imp.ch',
        'spamsources.fabel.dk',
        'ubl.unsubscore.com',
        'web.dnsbl.sorbs.net',
        'wormrbl.imp.ch',
        'z.mailspike.net',
        'zen.spamhaus.org',
        'zombie.dnsbl.sorbs.net',
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklist Cached IP Address Time
    |--------------------------------------------------------------------------
    | The amount of time in seconds that the server IP addresses are cached.
    | This is done because servers contain multiple domains and there is
    | no reason to re-run the IP address of the server multiple times.
    */

    'blacklist_cached_seconds' => 7200,

    /*
    |--------------------------------------------------------------------------
    | Lighthouse Audit Reports
    |--------------------------------------------------------------------------
    | Limit audits to run only after a certain amount of time.
    */

    'lighthouse_audits' => [
        'run_audit_every_hours' => 160,
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
