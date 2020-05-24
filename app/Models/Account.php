<?php

namespace App\Models;

use App\Filters\AccountFilters;
use App\Models\Concerns\Unguarded;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use Unguarded;

    protected $casts = ['backup' => 'boolean', 'suspended' => 'boolean'];
    protected $dates = ['suspend_time', 'setup_date'];
    protected $appends = ['disk_usage', 'disk_usage_raw', 'cpanel_url', 'whm_url', 'domain_url'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function scopeFilter($query, AccountFilters $filters)
    {
        return $filters->apply($query);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('domain', 'LIKE', '%' . $search . '%')
                ->orWhere('user', 'LIKE', '%' . $search . '%')
                ->orWhere('ip', 'LIKE', '%' . $search . '%');
        });
    }

    public function getDiskUsageAttribute()
    {
        if (is_null($this->disk_usage_raw)) {
            return 'n/a';
        }

        return $this->disk_usage_raw . '%';
    }

    public function getDiskUsageRawAttribute()
    {
        $diskUsed = substr($this->disk_used, 0, -1);
        $diskLimit = substr($this->disk_limit, 0, -1);

        if (! is_numeric($diskLimit)) {
            return null;
        }

        return round(($diskUsed / $diskLimit) * 100, 1);
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
