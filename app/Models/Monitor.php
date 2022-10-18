<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DowntimeStat[] $downtimeStats
 * @property-read int|null $downtime_stats_count
 * @property-read string $certificate_status_as_emoji
 * @property-read string $chunked_last_certificate_check_failure_reason
 * @property-read string $chunked_last_failure_reason
 * @property-read string $raw_url
 * @property-read string $uptime_status_as_emoji
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateCheckEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateCheckFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCertificateStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Monitor whereId($value)
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
 * @mixin \Eloquent
 */
class Monitor extends BaseMonitor
{
    public function downtimeStats(): HasMany
    {
        return $this->hasMany(DowntimeStat::class);
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

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('url', 'LIKE', '%'.$search.'%');
        });
    }
}
