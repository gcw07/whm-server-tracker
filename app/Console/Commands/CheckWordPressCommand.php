<?php

namespace App\Console\Commands;

use App\Jobs\CheckBlacklistJob;
use App\Jobs\CheckWordPressJob;
use App\Models\Account;
use App\Models\Monitor;
use Illuminate\Console\Command;

class CheckWordPressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server-tracker:check-wordpress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the account is using WordPress and get the version.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = Account::query()
            ->where('suspended', false)
            ->orderBy('domain')
            ->get();

        $this->comment('Start checking the accounts of '.count($accounts).' accounts...');

        $accounts->each(function (Account $account) {
            $this->info("Checking wordpress for $account->domain");

            dispatch(new CheckWordPressJob($account));
        });

        $this->info('All done!');
    }
}
