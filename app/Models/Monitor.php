<?php

namespace App\Models;

use App\Enums\BlacklistStatusEnum;
use App\Enums\DomainNameStatusEnum;
use App\Enums\LighthouseStatusEnum;
use App\Events\DomainNameExpiresSoonEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Lighthouse\Lighthouse;
use Spatie\UptimeMonitor\Models\Monitor as BaseMonitor;

/**
 * App\Models\Monitor
 *
 * @property int $id
 * @property \Spatie\Url\Url|null $url
 * @property bool $uptime_check_enabled
 * @property string $look_for_string
 * @property string $uptime_check_interval_in_minutes
 * @property string $uptime_status
 * @property string|null $uptime_check_failure_reason
 * @property int $uptime_check_times_failed_in_a_row
 * @property \Illuminate\Support\Carbon|null $uptime_status_last_change_date
 * @property \Illuminate\Support\Carbon|null $uptime_last_check_date
 * @property \Illuminate\Support\Carbon|null $uptime_check_failed_event_fired_on_date
 * @property string $uptime_check_method
 * @property string|null $uptime_check_payload
 * @property array $uptime_check_additional_headers
 * @property string|null $uptime_check_response_checker
 * @property bool $certificate_check_enabled
 * @property string $certificate_status
 * @property \Illuminate\Support\Carbon|null $certificate_expiration_date
 * @property string|null $certificate_issuer
 * @property string $certificate_check_failure_reason
 * @property bool $blacklist_check_enabled
 * @property string $blacklist_status
 * @property string|null $blacklist_check_failure_reason
 * @property bool $lighthouse_check_enabled
 * @property string $lighthouse_status
 * @property \Illuminate\Support\Carbon|null $lighthouse_update_last_failed_at
 * @property \Illuminate\Support\Carbon|null $lighthouse_update_last_succeeded_at
 * @property string|null $lighthouse_check_failure_reason
 * @property bool $domain_name_check_enabled
 * @property string $domain_name_status
 * @property \Illuminate\Support\Carbon|null $domain_name_expiration_date
 * @property string|null $domain_name_check_failure_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DowntimeStat[] $downtimeStats
 * @property-read int|null $downtime_stats_count
 * @property-read string $certificate_status_as_emoji
 * @property-read string $chunked_last_certificate_check_failure_reason
 * @property-read string $chunked_last_failure_reason
 * @property-read string $raw_url
 * @property-read string $uptime_status_as_emoji
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LighthouseAudit[] $lighthouseAudits
 * @property-read int|null $lighthouse_audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LighthouseAudit[] $lighthouseLatestAudit
 * @property-read int|null $lighthouse_latest_audit_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor search($search)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereBlacklistCheckEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereBlacklistCheckFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereBlacklistStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateCheckEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateCheckFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereDomainNameCheckEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereDomainNameCheckFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereDomainNameExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereDomainNameStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereLighthouseCheckEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereLighthouseCheckFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereLighthouseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereLighthouseUpdateLastFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereLighthouseUpdateLastSucceededAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereLookForString($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckAdditionalHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckFailedEventFiredOnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckIntervalInMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckPayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckResponseChecker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeCheckTimesFailedInARow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeLastCheckDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUptimeStatusLastChangeDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Monitor extends BaseMonitor
{
    protected $casts = [
        'uptime_check_enabled' => 'boolean',
        'uptime_last_check_date' => 'datetime',
        'uptime_status_last_change_date' => 'datetime',
        'uptime_check_failed_event_fired_on_date' => 'datetime',
        'certificate_check_enabled' => 'boolean',
        'certificate_expiration_date' => 'datetime',
        'lighthouse_check_enabled' => 'boolean',
        'lighthouse_update_last_failed_at' => 'datetime',
        'lighthouse_update_last_succeeded_at' => 'datetime',
        'domain_name_check_enabled' => 'boolean',
        'domain_name_expiration_date' => 'datetime',
        'blacklist_check_enabled' => 'boolean',
    ];

    public function downtimeStats(): HasMany
    {
        return $this->hasMany(DowntimeStat::class);
    }

    public function lighthouseAudits(): HasMany
    {
        return $this->hasMany(LighthouseAudit::class);
    }

    public function lighthouseLatestAudit(): HasMany
    {
        return $this->hasMany(LighthouseAudit::class)->orderBy('created_at', 'desc');
    }

    protected function uptimeForToday(): Attribute
    {
        $startDate = today()->format('Y-m-d');
        $endDate = today()->format('Y-m-d');

        return Attribute::make(
            get: fn () => $this->calculateUptime($startDate, $endDate),
        );
    }

    protected function uptimeForLastSevenDays(): Attribute
    {
        $startDate = today()->subDays(6)->format('Y-m-d');
        $endDate = today()->format('Y-m-d');

        return Attribute::make(
            get: fn () => $this->calculateUptime($startDate, $endDate),
        );
    }

    protected function uptimeForLastThirtyDays(): Attribute
    {
        $startDate = today()->subDays(29)->format('Y-m-d');
        $endDate = today()->format('Y-m-d');

        return Attribute::make(
            get: fn () => $this->calculateUptime($startDate, $endDate),
        );
    }

    public function calculateUptime($startDate, $endDate): float
    {
        $dates = CarbonPeriod::create($startDate, '1 day', $endDate);

        $stats = $this->downtimeStats()
            ->select([
                'date',
                DB::raw('SUM(downtime_period) as downtime'),
            ])
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->date->format('Y-m-d') => $item->downtime,
            ]);

        $uptimePercentage = collect($dates)->mapWithKeys(function ($item) use ($stats) {
            $date = $item->format('Y-m-d');

            if (isset($stats[$date])) {
                return [$date => 100 - round(($stats[$date] / 86400) * 100, 2)];
            }

            return [$date => 100];
        })->average();

        return round($uptimePercentage, 2);
    }

    public function checkBlacklist()
    {
        $blacklistServers = config('server-tracker.blacklist_servers');
        $cachedSeconds = config('server-tracker.blacklist_cached_seconds');

        try {
            $items =
                collect($this->checkBlacklistForIP($blacklistServers, $cachedSeconds))
                    ->merge($this->checkBlacklistForHostname($blacklistServers));

            $this->setBlacklist($items);
        } catch (Exception $exception) {
            $this->setBlacklistException($exception);
        }
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('url', 'LIKE', '%'.$search.'%');
        });
    }

    public function checkBlacklistForHostname($servers)
    {
        $reverseIp = implode('.', array_reverse(explode('.', $this->url->getHost())));

        $items = [];

        foreach ($servers as $host) {
            if (checkdnsrr($reverseIp.'.'.$host.'.', 'A')) {
                $foundOnList = true;
            } else {
                $foundOnList = false;
            }

            if ($foundOnList) {
                $items[] = [
                    'host' => $host,
                ];
            }
        }

        return $items;
    }

    public function checkBlacklistForIP($servers, $cachedSeconds)
    {
        $mxRecords = dns_get_record($this->url->getHost(), DNS_MX);
        $mxEntry = collect($mxRecords)->pluck('target')->first();
        $serverIP = gethostbyname($mxEntry);
        $reverseIp = implode('.', array_reverse(explode('.', $serverIP)));

        return Cache::remember($serverIP, $cachedSeconds, function () use ($servers, $reverseIp) {
            $items = [];

            foreach ($servers as $host) {
                if (checkdnsrr($reverseIp.'.'.$host.'.', 'A')) {
                    $foundOnList = true;
                } else {
                    $foundOnList = false;
                }

                if ($foundOnList) {
                    $items[] = [
                        'host' => $host,
                    ];
                }
            }

            return $items;
        });
    }

    public function setBlacklist($items): void
    {
        if (count($items) > 0) {
            $this->blacklist_status = BlacklistStatusEnum::Invalid->value;
            $this->blacklist_check_failure_reason = $this->getBlacklistFailureReason($items);
        } else {
            $this->blacklist_status = BlacklistStatusEnum::Valid->value;
            $this->blacklist_check_failure_reason = null;
        }

        $this->save();

//        event(new BlacklistCheckSucceeded($this, $exception->getMessage()));
    }

    public function setBlacklistException(Exception $exception): void
    {
        $this->blacklist_status = BlacklistStatusEnum::Invalid->value;
        $this->blacklist_check_failure_reason = $exception->getMessage();
        $this->save();

//        event(new BlacklistCheckFailed($this, $exception->getMessage()));
    }

    public function getBlacklistFailureReason($items): string
    {
        $reason = '';
        foreach ($items as $item) {
            $reason .= "Found on {$item['host']} blacklist.\n";
        }

        return $reason;
    }

    public function checkLighthouse(): void
    {
        if ($this->shouldRunLighthouseAudit()) {
            try {
                $result = Lighthouse::url($this->url)->run();
                $scores = $result->scores();
                $speed = $result->speedIndexInMs();
                $rawResults = json_encode($result->audits());
                $report = $result->html();

                LighthouseAudit::create([
                    'monitor_id' => $this->id,
                    'date' => today()->format('Y-m-d'),
                    'performance_score' => $scores['performance'],
                    'accessibility_score' => $scores['accessibility'],
                    'best_practices_score' => $scores['best-practices'],
                    'seo_score' => $scores['seo'],
                    'pwa_score' => $scores['pwa'],
                    'speed_index' => $speed,
                    'raw_results' => $rawResults,
                    'report' => $report,
                ]);

                $this->lighthouse_status = LighthouseStatusEnum::Valid->value;
                $this->lighthouse_update_last_succeeded_at = now();
                $this->save();
            } catch (Exception $exception) {
                $this->lighthouse_status = LighthouseStatusEnum::Invalid->value;
                $this->lighthouse_update_last_failed_at = now();
                $this->lighthouse_check_failure_reason = $exception->getMessage();
                $this->save();
            }
        }
    }

    protected function shouldRunLighthouseAudit(): bool
    {
        if (is_null($this->lighthouse_update_last_succeeded_at)) {
            return true;
        }

        if ($this->lighthouse_update_last_succeeded_at->diffInHours() >= config('server-tracker.lighthouse_audits.run_audit_every_hours')) {
            return true;
        }

        return false;
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
    }

    public function setDomainNameExpiration(Carbon $date): void
    {
        $this->domain_name_status = DomainNameStatusEnum::Valid->value;
        $this->domain_name_expiration_date = $date;
        $this->domain_name_check_failure_reason = null;
        $this->save();

        $this->fireEventsForUpdatedMonitorWithDomainName($this, $date);
    }

    public function setDomainNameException($reason): void
    {
        $this->domain_name_status = DomainNameStatusEnum::Invalid->value;
        $this->domain_name_check_failure_reason = $reason;
        $this->save();

//        event(new DomainNameCheckFailed($this, $exception->getMessage()));
    }

    public function fireEventsForUpdatedMonitorWithDomainName(Monitor $monitor, Carbon $date): void
    {
        if ($this->domain_name_status === DomainNameStatusEnum::Valid->value) {
            if ($date->diffInDays() <= config('server-tracker.domain_name_expires_within_days')) {
                event(new DomainNameExpiresSoonEvent($monitor, $date));
            }

            return;
        }

        if ($this->domain_name_status === DomainNameStatusEnum::Invalid->value) {
//        event(new DomainNameCheckSucceeded($this, $exception->getMessage()));
        }
    }
}
