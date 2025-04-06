<?php

return [

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'backups' => [
            'driver' => 'local',
            'root' => storage_path('app/backups'),
        ],
    ],

];
