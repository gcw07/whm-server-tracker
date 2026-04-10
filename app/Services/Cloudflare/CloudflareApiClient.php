<?php

namespace App\Services\Cloudflare;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CloudflareApiClient
{
    private const BASE_URL = 'https://api.cloudflare.com/client/v4/';

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
