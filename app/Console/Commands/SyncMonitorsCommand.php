<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\UptimeMonitor\Exceptions\CannotSaveMonitor;
use Spatie\UptimeMonitor\Models\Monitor;

class SyncMonitorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:sync-monitors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the uptime checker monitors with accounts in the system.';

    public function handle(): void
    {
        $accounts = $this->getAccounts();

        $this->validateMonitors($accounts);

        $this->createOrUpdateMonitorsFromAccounts($accounts);

        $this->deleteMissingMonitors($accounts);
    }

    protected function getAccounts()
    {
        return Account::query()
            ->select(['id', 'domain', 'suspended'])
            ->where('suspended', false)
            ->orderBy('domain')
            ->cursor();
    }

    protected function validateMonitors($accounts)
    {
        $accounts->each(function ($monitorAttributes) {
            if (! Str::startsWith($monitorAttributes['domain_url'], ['https://', 'http://'])) {
                throw new CannotSaveMonitor("URL `{$monitorAttributes['url']}` is invalid (is the URL scheme included?)");
            }
        });
    }

    protected function createOrUpdateMonitorsFromAccounts($accounts)
    {
        $accounts
            ->each(function ($account) {
                $monitor = $this->createOrUpdateMonitor([
                    'url' => $account->domain_url,
                ]);

                // Update account with monitor_id if not already set
                if ($account->monitor_id !== $monitor->id) {
                    $account->update(['monitor_id' => $monitor->id]);
                }
            });

        $this->info("Synced {$accounts->count()} monitor(s) to database");
    }

    protected function createOrUpdateMonitor(array $monitorAttributes)
    {
        return Monitor::firstOrCreate(
            ['url' => $monitorAttributes['url']],
            $monitorAttributes
        );
    }

    protected function deleteMissingMonitors($accounts)
    {
        // Get all monitor IDs that should exist based on accounts
        $validMonitorIds = $accounts->pluck('monitor_id')->filter()->unique();

        // Delete monitors that are not associated with any accounts
        Monitor::whereNotIn('id', $validMonitorIds)
            ->each(function (Monitor $monitor) {
                $this->comment("Deleted monitor for `{$monitor->url}` from database because it was not found in account listings.");
                $monitor->delete();
            });
    }
}
