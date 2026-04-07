<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Services\Lighthouse\LighthouseAuditRunner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Lighthouse\Enums\FormFactor;

#[Tries(5)]
class CheckLighthouseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Monitor $monitor,
        public FormFactor $formFactor,
    ) {
        $this->onQueue('long-timeout');
    }

    public function handle(LighthouseAuditRunner $runner): void
    {
        if (! $runner->shouldRun($this->monitor, $this->formFactor)) {
            return;
        }

        $runner->run($this->monitor, $this->formFactor);
    }
}
