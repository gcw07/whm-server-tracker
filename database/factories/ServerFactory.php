<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Server;
use Faker\Generator as Faker;

$factory->define(Server::class, function (Faker $faker) {
    return [
        'name' => $faker->domainName,
        'address' => $faker->ipv4,
        'port' => '2087',
        'server_type' => $faker->randomElement(['vps', 'dedicated', 'reseller']),
        'settings' => []
    ];
});
