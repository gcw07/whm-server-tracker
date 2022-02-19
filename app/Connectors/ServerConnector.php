<?php

namespace App\Connectors;

interface ServerConnector
{
    public function setServer($server);
    public function setTimeout($seconds);

    public function getDiskUsage();
    public function getBackups();
    public function getPhpDefaultVersion();
    public function getPhpVersions();
    public function getAccounts();
    public function getSystemLoadAvg();
}
