<?php

return [

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'backups' => [
            'driver' => 'local',
            'root' => storage_path('app/backups'),
        ],
    ],

];
