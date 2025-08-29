<?php

return [

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],

    'encryption_key' => env('APP_ENCRYPTION_KEY'),

];
