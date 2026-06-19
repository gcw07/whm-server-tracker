<?php

namespace App\Jobs;

use App\Models\MonitorSearchConsoleCheck;
use App\Services\Google\GoogleApiClientService;
use Google\Service\Exception as GoogleServiceException;
use Google\Service\SiteVerification;
use Google\Service\SiteVerification\SiteVerificationWebResourceGettokenRequest;
use Google\Service\SiteVerification\SiteVerificationWebResourceGettokenRequestSite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(3)]
class FetchSearchConsoleDnsTxtJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public MonitorSearchConsoleCheck $check)
    {
        $this->onQueue('low');
    }

    public function handle(GoogleApiClientService $googleClient): void
    {
        $domain = str_replace('sc-domain:', '', $this->check->domain_property);

        $client = $googleClient->getClient();
        $siteVerification = new SiteVerification($client);

        $site = new SiteVerificationWebResourceGettokenRequestSite;
        $site->setType('INET_DOMAIN');
        $site->setIdentifier($domain);

        $request = new SiteVerificationWebResourceGettokenRequest;
        $request->setVerificationMethod('DNS_TXT');
        $request->setSite($site);

        try {
            $response = $siteVerification->webResource->getToken($request);
        } catch (GoogleServiceException $e) {
            if ($this->isQuotaError($e)) {
                $this->release(60);

                return;
            }

            throw $e;
        }

        $this->check->update([
            'dns_txt_record' => $response->getToken(),
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
