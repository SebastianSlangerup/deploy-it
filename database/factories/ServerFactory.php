<?php

namespace Database\Factories;

use App\Models\Configuration;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'ip' => $this->faker->ipv4(),
            'configuration_id' => Configuration::factory(),
        ];
    }
}
