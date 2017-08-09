<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Account::class, function (Faker $faker) {
    $domain = $faker->domainName;
    $user = explode('.', $domain)[0];
    $suspended = $faker->boolean(10);

    return [
        'server_id' => function () {
            return factory(App\Server::class)->create()->id;
        },
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
});
