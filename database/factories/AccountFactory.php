<?php

use Faker\Generator as Faker;

$factory->define(App\Account::class, function (Faker $faker) {
    $domain = $faker->domainName;
    $user = explode('.', $domain)[0];

    return [
        'server_id' => function () {
            return factory(App\Server::class)->create()->id;
        },
        'domain' => $domain,
        'user' => $user,
        'ip' => $faker->ipv4,
        'backup' => $faker->boolean(90),
        'suspended' => $faker->boolean(5),
        'suspend_reason' => 'not suspended',
        'suspend_time' => null,
        'setup_date' => $faker->dateTimeBetween('-1 year', '-2 weeks'),
        'disk_used' => $faker->randomElement(['250M', '500M', '800M', '950M']),
        'disk_limit' => $faker->randomElement(['1000M', '3000M', '5000M', '10000M']),
        'plan' => $faker->randomElement(['1 Gig', '3 Gig', '5 Gig', '10 Gig']),
    ];
});
