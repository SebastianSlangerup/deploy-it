<?php

namespace Database\Factories;

use App\Models\Configuration;
use App\Models\Container;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ContainerFactory extends Factory
{
    protected $model = Container::class;

    public function definition(): array
    {
        return [
            'docker_image' => $this->faker->url(),
            'server_id' => Server::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
