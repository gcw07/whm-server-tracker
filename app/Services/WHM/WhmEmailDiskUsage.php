<?php

namespace App\Services\WHM;

use App\Services\WHM\DataProcessors\ProcessAccountEmails;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class WhmEmailDiskUsage extends WhmApiBase
{
    public function fetch(): void
    {
        $accounts = $this->server->accounts()->get();

        if ($accounts->isEmpty()) {
            return;
        }

        $responses = Http::pool(fn (Pool $pool) => $accounts->flatMap(fn ($account) => [
            $this->configuredRequest($pool, $account->user)
                ->get('cpanel', [
                    'cpanel_jsonapi_user' => $account->user,
                    'cpanel_jsonapi_module' => 'Email',
                    'cpanel_jsonapi_func' => 'list_pops_with_disk',
                    'cpanel_jsonapi_apiversion' => 3,
                ]),
            $this->configuredRequest($pool, "{$account->user}_system")
                ->get('cpanel', [
                    'cpanel_jsonapi_user' => $account->user,
                    'cpanel_jsonapi_module' => 'Email',
                    'cpanel_jsonapi_func' => 'get_main_account_disk_usage_bytes',
                    'cpanel_jsonapi_apiversion' => 3,
                ]),
        ])->all());

        foreach ($accounts as $account) {
            $emailsResponse = $responses[$account->user] ?? null;
            $systemResponse = $responses["{$account->user}_system"] ?? null;

            if (! $emailsResponse || $emailsResponse instanceof \Exception || $emailsResponse->failed()) {
                continue;
            }

            $emailData = $emailsResponse->json();

            if ($systemResponse && ! ($systemResponse instanceof \Exception) && ! $systemResponse->failed()) {
                $systemBytes = $systemResponse->json()['result']['data'] ?? 0;
                $emailData['result']['data'][] = [
                    'email' => $account->user,
                    'user' => 'system',
                    'domain' => $account->domain,
                    '_diskused' => $systemBytes,
                    '_diskquota' => 0,
                    'diskusedpercent_float' => 0,
                    'suspended_incoming' => 0,
                    'suspended_login' => 0,
                ];
            }

            (new ProcessAccountEmails)->execute($account, $emailData);
        }
    }
}
