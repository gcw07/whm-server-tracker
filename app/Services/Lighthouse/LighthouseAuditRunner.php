<?php

namespace App\Services\Lighthouse;

use App\Enums\LighthouseStatusEnum;
use App\Models\LighthouseAudit;
use App\Models\Monitor;
use App\Models\MonitorLighthouseCheck;
use Exception;
use Spatie\Lighthouse\Enums\FormFactor;
use Spatie\Lighthouse\Lighthouse;

class LighthouseAuditRunner
{
    public function shouldRun(Monitor $monitor, FormFactor $formFactor): bool
    {
        $check = $this->lighthouseCheck($monitor, $formFactor);

        if (is_null($check->last_succeeded_at)) {
            return true;
        }

        return $check->last_succeeded_at
            ->addDays(config('server-tracker.lighthouse_audits.run_audit_every_days'))
            ->isPast();
    }

    public function run(Monitor $monitor, FormFactor $formFactor): void
    {
        $timeout = config('server-tracker.lighthouse_audits.audit_timeout');
        $check = $this->lighthouseCheck($monitor, $formFactor);

        try {
            $result = Lighthouse::url($monitor->url)
                ->timeoutInSeconds($timeout)
                ->formFactor($formFactor)
                ->withChromeOptions([
                    'chromeFlags' => [
                        '--headless=new',
                        '--no-sandbox',
                        '--disable-gpu',
                        '--disable-dev-shm-usage',
                    ],
                ])
                ->run();

            $scores = $result->scores();

            LighthouseAudit::create([
                'monitor_id' => $monitor->id,
                'performance_score' => $scores['performance'],
                'accessibility_score' => $scores['accessibility'],
                'best_practices_score' => $scores['best-practices'],
                'seo_score' => $scores['seo'],
                'speed_index' => $result->speedIndexInMs(),
                'first_contentful_paint' => $result->firstContentfulPaintInMs(),
                'largest_contentful_paint' => $result->largestContentfulPaintInMs(),
                'time_to_interactive' => $result->timeToInteractiveInMs(),
                'total_blocking_time' => $result->totalBlockingTimeInMs(),
                'cumulative_layout_shift' => $result->cumulativeLayoutShift(),
                'form_factor' => $result->formFactor(),
                'raw_results' => json_encode($result->audits()),
            ]);

            $check->update([
                'status' => LighthouseStatusEnum::Valid->value,
                'last_succeeded_at' => now(),
            ]);
        } catch (Exception $exception) {
            $check->update([
                'status' => LighthouseStatusEnum::Invalid->value,
                'last_failed_at' => now(),
                'failure_reason' => $exception->getMessage(),
            ]);
        }
    }

    private function lighthouseCheck(Monitor $monitor, FormFactor $formFactor): MonitorLighthouseCheck
    {
        return MonitorLighthouseCheck::where('monitor_id', $monitor->id)
            ->where('form_factor', $formFactor->value)
            ->firstOrFail();
    }
}
