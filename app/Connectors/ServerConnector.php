<?php

namespace App\Connectors;

interface ServerConnector
{
    public function __construct($server);
    public static function create($server);
    
    public function setTimeout($seconds);

    public function getDiskUsage();
    public function getBackups();
    public function getAccounts();
    public function getSystemLoadAvg();
}
