<?php

return [
    'slug' => 'wp-tracker-agent',
    'name' => 'WP Tracker Agent',
    'version' => env('PLUGIN_UPDATER_VERSION', '0.9.0'),
    'requires' => '6.0',
    'tested' => env('PLUGIN_UPDATER_TESTED', '6.7'),
    'zip_path' => env('PLUGIN_UPDATER_ZIP_PATH'),
];
