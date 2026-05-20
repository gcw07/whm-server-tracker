<?php

namespace App\Console\Commands;

use App\Models\LighthouseAudit;
use App\Models\MonitorLighthouseCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('server-tracker:prune-lighthouse-audits')]
#[Description('Null out raw_results for old audits and delete rows beyond the configured retention window')]
class PruneLighthouseAuditsCommand extends Command
{
    public function handle(): void
    {
        $keepCount = config('server-tracker.lighthouse_audits.keep_full_details_count', 4);
        $pruneAfterMonths = config('server-tracker.lighthouse_audits.prune_rows_after_months');

        $nulled = 0;
        $deleted = 0;

        MonitorLighthouseCheck::select('monitor_id', 'form_factor')->get()
            ->each(function (MonitorLighthouseCheck $check) use ($keepCount, &$nulled): void {
                $recentIds = LighthouseAudit::where('monitor_id', $check->monitor_id)
                    ->where('form_factor', $check->form_factor)
                    ->orderByDesc('created_at')
                    ->limit($keepCount)
                    ->pluck('id');

                $nulled += LighthouseAudit::where('monitor_id', $check->monitor_id)
                    ->where('form_factor', $check->form_factor)
                    ->whereNotNull('raw_results')
                    ->when($recentIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $recentIds))
                    ->update(['raw_results' => null]);
            });

        if ($pruneAfterMonths) {
            $deleted = LighthouseAudit::where('created_at', '<', now()->subMonths($pruneAfterMonths))->delete();
        }

        $this->line("Lighthouse audits pruned: {$nulled} raw_results cleared, {$deleted} rows deleted.");
    }
}
