<?php

namespace Tests\Factories;

use App\Models\Account;
use App\Models\Server;
use Carbon\Carbon;
use Christophrumpel\LaravelFactoriesReloaded\BaseFactory;
use Faker\Generator as Faker;

class AccountFactory extends BaseFactory
{

    protected string $modelClass = Account::class;

    public function create(array $extra = []): Account
    {
        return parent::build($extra);
    }

    public function make(array $extra = []): Account
    {
        return parent::build($extra, 'make');
    }

    public function getDefaults(Faker $faker): array
    {
        $domain = $faker->domainName;
        $user = explode('.', $domain)[0];
        $suspended = $faker->boolean(10);

        return [
            'server_id' => factory(Server::class),
            'domain' => $domain,
            'user' => $user,
            'ip' => $faker->ipv4,
            'backup' => $faker->boolean(90),
            'suspended' => $suspended,
            'suspend_reason' => $suspended ? 'account unpaid' : 'not suspended',
            'suspend_time' => $suspended ? Carbon::now()->subDays(15) : null,
            'setup_date' => $faker->dateTimeBetween('-1 year', '-2 weeks'),
            'disk_used' => $faker->randomElement(['250M', '500M', '800M', '950M']),
            'disk_limit' => $faker->randomElement(['1000M', '3000M', '5000M', '10000M']),
            'plan' => $faker->randomElement(['1 Gig', '3 Gig', '5 Gig', '10 Gig']),
        ];
    }

}

