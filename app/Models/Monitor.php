<?php

namespace App\Models;

use App\Enums\DomainNameStatusEnum;
use App\Enums\WordPressStatusEnum;
use App\Events\DomainNameExpiresSoonEvent;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;
use Spatie\UptimeMonitor\Models\Monitor as BaseMonitor;
use Spatie\Url\Url;

/**
 * @property int $id
 * @property Url|null $url
 * @property bool $uptime_check_enabled
 * @property string $look_for_string
 * @property string $uptime_check_interval_in_minutes
 * @property string $uptime_status
 * @property string|null $uptime_check_failure_reason
 * @property int $uptime_check_times_failed_in_a_row
 * @property CarbonImmutable|null $uptime_status_last_change_date
 * @property CarbonImmutable|null $uptime_last_check_date
 * @property CarbonImmutable|null $uptime_check_failed_event_fired_on_date
 * @property string $uptime_check_method
 * @property string|null $uptime_check_payload
 * @property array $uptime_check_additional_headers
 * @property string|null $uptime_check_response_checker
 * @property bool $certificate_check_enabled
 * @property string $certificate_status
 * @property CarbonImmutable|null $certificate_expiration_date
 * @property string|null $certificate_issuer
 * @property string $certificate_check_failure_reason
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read mixed $account_ssl_certificate_status
 * @property-read Collection<int, Account> $accounts
 * @property-read int|null $accounts_count
 * @property-read MonitorBlacklistCheck|null $blacklistCheck
 * @property-read MonitorDomainCheck|null $domainCheck
 * @property-read mixed $domain_name
 * @property-read string $certificate_status_as_emoji
 * @property-read string $chunked_last_certificate_check_failure_reason
 * @property-read string $chunked_last_failure_reason
 * @property-read string $raw_url
 * @property-read string $uptime_status_as_emoji
 * @property-read Collection<int, LighthouseAudit> $lighthouseAudits
 * @property-read int|null $lighthouse_audits_count
 * @property-read MonitorLighthouseCheck|null $lighthouseCheck
 * @property-read Collection<int, LighthouseAudit> $lighthouseLatestAudit
 * @property-read int|null $lighthouse_latest_audit_count
 * @property-read Collection<int, MonitorOutage> $outages
 * @property-read int|null $outages_count
 * @property-read mixed $uptime_for_last_seven_days
 * @property-read mixed $uptime_for_last_thirty_days
 * @property-read mixed $uptime_for_today
 * @property-read MonitorWordPressCheck|null $wordpressCheck
 * @property-read MonitorCloudflareCheck|null $cloudflareCheck
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monitor enabled()
 * @method static Builder<static>|Monitor newModelQuery()
 * @method static Builder<static>|Monitor newQuery()
 * @method static Builder<static>|Monitor query()
 * @method static Builder<static>|Monitor search(string $term)
 * @method static Builder<static>|Monitor whereCertificateCheckEnabled($value)
 * @method static Builder<static>|Monitor whereCertificateCheckFailureReason($value)
 * @method static Builder<static>|Monitor whereCertificateExpirationDate($value)
 * @method static Builder<static>|Monitor whereCertificateIssuer($value)
 * @method static Builder<static>|Monitor whereCertificateStatus($value)
 * @method static Builder<static>|Monitor whereCreatedAt($value)
 * @method static Builder<static>|Monitor whereId($value)
 * @method static Builder<static>|Monitor whereLookForString($value)
 * @method static Builder<static>|Monitor whereUpdatedAt($value)
 * @method static Builder<static>|Monitor whereUptimeCheckAdditionalHeaders($value)
 * @method static Builder<static>|Monitor whereUptimeCheckEnabled($value)
 * @method static Builder<static>|Monitor whereUptimeCheckFailedEventFiredOnDate($value)
 * @method static Builder<static>|Monitor whereUptimeCheckFailureReason($value)
 * @method static Builder<static>|Monitor whereUptimeCheckIntervalInMinutes($value)
 * @method static Builder<static>|Monitor whereUptimeCheckMethod($value)
 * @method static Builder<static>|Monitor whereUptimeCheckPayload($value)
 * @method static Builder<static>|Monitor whereUptimeCheckResponseChecker($value)
 * @method static Builder<static>|Monitor whereUptimeCheckTimesFailedInARow($value)
 * @method static Builder<static>|Monitor whereUptimeLastCheckDate($value)
 * @method static Builder<static>|Monitor whereUptimeStatus($value)
 * @method static Builder<static>|Monitor whereUptimeStatusLastChangeDate($value)
 * @method static Builder<static>|Monitor whereUrl($value)
 * @method static Builder<static>|Monitor withIssues()
 *
 * @mixin \Eloquent
 */
class Monitor extends BaseMonitor
{
    protected $appends = ['domain_name', 'raw_url'];

    protected $casts = [
        'uptime_check_enabled' => 'boolean',
        'uptime_last_check_date' => 'datetime',
        'uptime_status_last_change_date' => 'datetime',
        'uptime_check_failed_event_fired_on_date' => 'datetime',
        'certificate_check_enabled' => 'boolean',
        'certificate_expiration_date' => 'datetime',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function outages(): HasMany
    {
        return $this->hasMany(MonitorOutage::class);
    }

    public function lighthouseAudits(): HasMany
    {
        return $this->hasMany(LighthouseAudit::class);
    }

    public function lighthouseLatestAudit(): HasMany
    {
        return $this->hasMany(LighthouseAudit::class)->orderBy('created_at', 'desc');
    }

    public function blacklistCheck(): HasOne
    {
        return $this->hasOne(MonitorBlacklistCheck::class);
    }

    public function lighthouseCheck(): HasOne
    {
        return $this->hasOne(MonitorLighthouseCheck::class)->where('form_factor', 'desktop');
    }

    public function lighthouseChecks(): HasMany
    {
        return $this->hasMany(MonitorLighthouseCheck::class);
    }

    public function domainCheck(): HasOne
    {
        return $this->hasOne(MonitorDomainCheck::class);
    }

    public function wordpressCheck(): HasOne
    {
        return $this->hasOne(MonitorWordPressCheck::class);
    }

    public function cloudflareCheck(): HasOne
    {
        return $this->hasOne(MonitorCloudflareCheck::class);
    }

    protected function domainName(): Attribute
    {
        return Attribute::make(
            get: fn () => preg_replace('(^https?://)', '', $this->url),
        );
    }

    protected function accountSslCertificateStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                $domain = $this->domain_name;
                $worstStatus = null;

                foreach ($this->accounts as $account) {
                    foreach ($account->sslCertificates as $cert) {
                        if ($cert->servername !== $domain && ! in_array($domain, $cert->certificate_domains ?? [])) {
                            continue;
                        }

                        if (is_null($cert->expires_at)) {
                            continue;
                        }

                        if ($cert->expires_at->isPast()) {
                            return 'expired';
                        }

                        if ($cert->expires_at->diffInDays(now(), false) >= -29) {
                            $worstStatus = 'expiring_soon';
                        } elseif ($worstStatus === null) {
                            $worstStatus = 'ok';
                        }
                    }
                }

                return $worstStatus;
            }
        );
    }

    protected function uptimeForToday(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->calculateUptime(today(), today()),
        );
    }

    protected function uptimeForLastSevenDays(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->calculateUptime(today()->subDays(6), today()),
        );
    }

    protected function uptimeForLastThirtyDays(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->calculateUptime(today()->subDays(29), today()),
        );
    }

    public function resetUptimeStatus(): void
    {
        $this->uptime_status = UptimeStatus::NOT_YET_CHECKED;
        $this->uptime_status_last_change_date = null;
        $this->uptime_check_failed_event_fired_on_date = null;
        $this->uptime_check_times_failed_in_a_row = 0;
        $this->uptime_check_failure_reason = null;
    }

    public function calculateUptime(CarbonInterface $startDate, CarbonInterface $endDate): float
    {
        $windowStart = $startDate->startOfDay();
        $windowEnd = $endDate->endOfDay();
        $totalPossibleSeconds = ($startDate->diffInDays($endDate) + 1) * 86400;

        $totalDowntimeSeconds = $this->outages()
            ->where('started_at', '<', $windowEnd)
            ->where('ended_at', '>', $windowStart)
            ->sum('duration_seconds');

        return round((($totalPossibleSeconds - $totalDowntimeSeconds) / $totalPossibleSeconds) * 100, 2);
    }

    #[Scope]
    public function withIssues(Builder $query): void
    {
        $query->where(function ($q) {
            $q->where(function ($inner) {
                $inner->where('uptime_check_enabled', true)
                    ->where('uptime_status', 'down');
            })->orWhere(function ($certQuery) {
                $certQuery->where('certificate_check_enabled', true)
                    ->whereHas('accounts', function ($accountsQuery) {
                        $accountsQuery->where('suspended', false)
                            ->whereHas('sslCertificates', function ($certs) {
                                $certs->whereNotNull('expires_at')
                                    ->where('expires_at', '<=', now()->addDays(29));
                            });
                    });
            });
        });
    }

    #[Scope]
    public function search(Builder $query, string $term): void
    {
        $query->whereAny([
            'url',
        ], 'LIKE', "%$term%");
    }

    public function processDomainNameExpiration($response)
    {
        if ($response->failed()) {
            $this->setDomainNameException($response->reason());

            return;
        }

        foreach ($response->object()->events as $event) {
            if ($event->eventAction === 'expiration') {
                $this->setDomainNameExpiration(Carbon::parse($event->eventDate));
                break;
            }
        }

        $nameservers = collect($response->object()->nameservers)
            ->pluck('ldhName')
            ->map(fn (string $name) => Str::lower($name))
            ->toArray();

        $this->setNameserverInfo($nameservers);
    }

    public function setDomainNameExpiration(Carbon $date): void
    {
        $this->domainCheck->update([
            'status' => DomainNameStatusEnum::Valid->value,
            'expiration_date' => $date,
            'failure_reason' => null,
        ]);

        $this->fireEventsForUpdatedMonitorWithDomainName($this, $date);
    }

    public function setDomainNameException($reason): void
    {
        $this->domainCheck->update([
            'status' => DomainNameStatusEnum::Invalid->value,
            'failure_reason' => $reason,
        ]);

        //        event(new DomainNameCheckFailed($this, $exception->getMessage()));
    }

    public function fireEventsForUpdatedMonitorWithDomainName(Monitor $monitor, Carbon $date): void
    {
        $domainStatus = $this->domainCheck->status;

        if ($domainStatus === DomainNameStatusEnum::Valid) {
            if ((int) abs($date->diffInDays()) <= config('server-tracker.domain_name_expires_within_days')) {
                event(new DomainNameExpiresSoonEvent($monitor, $date));
            }

            return;
        }

        if ($domainStatus === DomainNameStatusEnum::Invalid) {
            //        event(new DomainNameCheckSucceeded($this, $exception->getMessage()));
        }
    }

    public function setNameserverInfo(array $nameservers): void
    {
        $isOnCloudflare = Str::contains(collect($nameservers)->first(), 'cloudflare.com');

        $this->domainCheck->update([
            'nameservers' => $nameservers,
            'is_on_cloudflare' => $isOnCloudflare,
        ]);
    }

    public function checkWordPress(): void
    {
        try {
            $response = Http::timeout(30)->get((string) $this->url.'/feed/');

            if (! $response->ok()) {
                $this->setWordPress(null);

                return;
            }

            $xml = simplexml_load_string($response->body());

            if ($xml === false) {
                $this->setWordPress(null);
            } elseif ($xml->channel->generator && str_contains((string) $xml->channel->generator, '?v=')) {
                [, $version] = explode('?v=', (string) $xml->channel->generator);
                $this->setWordPress($version);
            } else {
                $this->setWordPress(null);
            }
        } catch (Exception $exception) {
            $this->setWordPressException($exception);
        }
    }

    public function setWordPress(?string $version): void
    {
        $this->wordpressCheck->update([
            'status' => WordPressStatusEnum::Valid->value,
            'wordpress_version' => $version,
            'failure_reason' => null,
        ]);
    }

    public function setWordPressException(Exception $exception): void
    {
        $this->wordpressCheck->update([
            'status' => WordPressStatusEnum::Invalid->value,
            'failure_reason' => $exception->getMessage(),
        ]);
    }
}
