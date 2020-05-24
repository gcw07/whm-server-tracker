<?php

namespace Tests\Factories;

use App\Models\Login;
use Christophrumpel\LaravelFactoriesReloaded\BaseFactory;
use Faker\Generator as Faker;

class LoginFactory extends BaseFactory
{

    protected string $modelClass = Login::class;

    public function create(array $extra = []): Login
    {
        return parent::build($extra);
    }

    public function make(array $extra = []): Login
    {
        return parent::build($extra, 'make');
    }

    public function getDefaults(Faker $faker): array
    {
        return [];
    }

}

