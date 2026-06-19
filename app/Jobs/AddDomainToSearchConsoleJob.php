<?php

namespace App\Jobs;

use App\Models\MonitorSearchConsoleCheck;
use App\Services\Google\GoogleApiClientService;
use Google\Service\Exception as GoogleServiceException;
use Google\Service\SearchConsole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(3)]
class AddDomainToSearchConsoleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public MonitorSearchConsoleCheck $check)
    {
        $this->onQueue('low');
    }

    public function handle(GoogleApiClientService $googleClient): void
    {
        $client = $googleClient->getClient();
        $gscService = new SearchConsole($client);

        try {
            $gscService->sites->add($this->check->domain_property);
        } catch (GoogleServiceException $e) {
            if ($this->isQuotaError($e)) {
                $this->release(60);

                return;
            }

            // 409 means the domain property already exists — treat as success.
            if ($e->getCode() !== 409) {
                throw $e;
            }
        }

        $this->check->update([
            'has_domain_property' => true,
            'last_synced_at' => now(),
        ]);
    }

    public function retryUntil(): \DateTimeInterface
    {
        return now()->addHours(12);
    }

    private function isQuotaError(GoogleServiceException $e): bool
    {
        $reason = $e->getErrors()[0]['reason'] ?? '';

        return in_array($reason, ['rateLimitExceeded', 'userRateLimitExceeded'])
            || str_contains($e->getMessage(), 'Quota exceeded');
    }
}
