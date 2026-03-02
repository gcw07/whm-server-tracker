<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Account;
use App\Models\AccountEmail;

class ProcessAccountEmails
{
    public function execute(Account $account, array $data): void
    {
        if (! isset($data['result']['data'])) {
            return;
        }

        $emails = $data['result']['data'];

        $emailAddresses = collect($emails)->map(fn ($item) => $item['email'])->all();

        collect($emails)->each(function ($item) use ($account) {
            AccountEmail::updateOrCreate(
                ['account_id' => $account->id, 'email' => $item['email']],
                [
                    'user' => $item['user'],
                    'domain' => $item['domain'],
                    'disk_used' => $item['_diskused'] ?? 0,
                    'disk_quota' => ($item['diskquota'] === 'unlimited' || $item['diskquota'] === '0') ? null : (int) $item['diskquota'],
                    'disk_used_percent' => $item['diskusedpercent_float'] ?? 0,
                    'suspended_incoming' => (bool) ($item['suspended_incoming'] ?? false),
                    'suspended_login' => (bool) ($item['suspended_login'] ?? false),
                ]
            );
        });

        AccountEmail::where('account_id', $account->id)
            ->whereNotIn('email', $emailAddresses)
            ->delete();
    }
}
