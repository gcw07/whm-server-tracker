<?php

namespace App\Jobs;

use App\Models\MonitorSearchConsoleCheck;
use App\Services\Cloudflare\CloudflareApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(3)]
class AddGscDnsRecordToCloudflareJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public MonitorSearchConsoleCheck $check)
    {
        $this->onQueue('low');
    }

    public function handle(CloudflareApiClient $cloudflare): void
    {
        $zoneId = $this->check->monitor->cloudflareCheck->cloudflare_zone_id;
        $domain = str_replace('sc-domain:', '', $this->check->domain_property);

        $existing = $cloudflare->listDnsRecords($zoneId, 'TXT', $this->check->dns_txt_record);

        if ($existing->isEmpty()) {
            $cloudflare->addDnsRecord($zoneId, 'TXT', $domain, $this->check->dns_txt_record);
        }

        $this->check->update([
            'dns_added_to_cloudflare' => true,
            'last_synced_at' => now(),
        ]);
    }
}
