<?php

namespace App\Services\Cloudflare;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CloudflareApiClient
{
    private const BASE_URL = 'https://api.cloudflare.com/client/v4/';

    private const GRAPHQL_URL = 'https://api.cloudflare.com/client/v4/graphql';

    private const ANALYTICS_CHUNK_SIZE = 10;

    /**
     * Fetch daily analytics for the given zone IDs from the Cloudflare GraphQL API.
     *
     * Zones are chunked into groups of 25 to stay within API limits. Returns a
     * collection keyed by zone ID containing aggregated traffic and bot breakdown data.
     *
     * @param  array<string>  $zoneIds
     * @return Collection<string, array{unique_visitors: int, requests_total: int, bandwidth_total: int}>
     */
    public function fetchDailyAnalytics(array $zoneIds, string $date): Collection
    {
        $results = collect();

        collect($zoneIds)
            ->chunk(self::ANALYTICS_CHUNK_SIZE)
            ->each(function (Collection $chunk) use ($date, $results) {
                $response = Http::withToken(config('services.cloudflare.api_token'))
                    ->timeout(15)
                    ->connectTimeout(10)
                    ->retry(3, 500)
                    ->post(self::GRAPHQL_URL, [
                        'query' => '
                            query ($zoneTags: [string!], $date: Date!) {
                                viewer {
                                    zones(filter: { zoneTag_in: $zoneTags }) {
                                        zoneTag
                                        httpRequests1dGroups(
                                            limit: 1
                                            filter: { date: $date }
                                        ) {
                                            sum { requests bytes }
                                            uniq { uniques }
                                        }
                                    }
                                }
                            }
                        ',
                        'variables' => [
                            'zoneTags' => $chunk->values()->all(),
                            'date' => $date,
                        ],
                    ]);

                $response->throw();

                foreach ($response->json('data.viewer.zones', []) as $zone) {
                    $day = $zone['httpRequests1dGroups'][0] ?? null;

                    $results->put($zone['zoneTag'], [
                        'unique_visitors' => $day['uniq']['uniques'] ?? 0,
                        'requests_total' => $day['sum']['requests'] ?? 0,
                        'bandwidth_total' => $day['sum']['bytes'] ?? 0,
                    ]);
                }
            });

        return $results;
    }

    /**
     * Fetch all zones from the Cloudflare account.
     *
     * Returns a collection keyed by domain name with zone id and status.
     *
     * @return Collection<string, array{id: string, status: string}>
     */
    public function fetchAllZones(): Collection
    {
        $zones = collect();
        $page = 1;

        do {
            $response = Http::withToken(config('services.cloudflare.api_token'))
                ->baseUrl(self::BASE_URL)
                ->timeout(30)
                ->connectTimeout(10)
                ->retry(3, 500)
                ->get('zones', ['page' => $page, 'per_page' => 1000]);

            $response->throw();

            $data = $response->json();

            collect($data['result'])->each(function (array $zone) use ($zones) {
                $zones->put($zone['name'], [
                    'id' => $zone['id'],
                    'status' => $zone['status'],
                ]);
            });

            $totalPages = (int) ceil($data['result_info']['total_count'] / $data['result_info']['per_page']);
            $page++;
        } while ($page <= $totalPages);

        return $zones;
    }
}
