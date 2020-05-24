<?php

namespace Tests\Factories;

use App\Models\Server;
use Christophrumpel\LaravelFactoriesReloaded\BaseFactory;
use Faker\Generator as Faker;

class ServerFactory extends BaseFactory
{

    protected string $modelClass = Server::class;

    public function create(array $extra = []): Server
    {
        return parent::build($extra);
    }

    public function make(array $extra = []): Server
    {
        return parent::build($extra, 'make');
    }

    public function getDefaults(Faker $faker): array
    {
        return [
            'name' => $faker->domainName,
            'address' => $faker->ipv4,
            'port' => '2087',
            'server_type' => $faker->randomElement(['vps', 'dedicated', 'reseller']),
            'settings' => []
        ];

    }

}

