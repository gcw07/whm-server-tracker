<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];
    protected $casts = ['backup' => 'boolean', 'suspended' => 'boolean'];
    protected $dates = ['suspend_time', 'setup_date'];
    protected $appends = ['disk_usage', 'cpanel_url', 'whm_url', 'domain_url'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function getDiskUsageAttribute()
    {
        $diskUsed = substr($this->disk_used, 0, -1);
        $diskLimit = substr($this->disk_limit, 0, -1);

        return round(($diskUsed / $diskLimit) * 100, 1) . '%';
    }

    public function getCpanelUrlAttribute()
    {
        return "https://{$this->domain}/cpanel";
    }

    public function getWhmUrlAttribute()
    {
        if ($this->server->port == 2087) {
            return "https://{$this->server->address}:{$this->server->port}";
        }

        return "http://{$this->server->address}:{$this->server->port}";
    }

    public function getDomainUrlAttribute()
    {
        return "http://{$this->domain}";
    }
}
