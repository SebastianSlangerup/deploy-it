<?php

namespace Database\Factories;

use App\Models\Configuration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ConfigurationFactory extends Factory
{
    protected $model = Configuration::class;

    public function definition(): array
    {
        return [
            'cores' => $this->faker->randomNumber('1'),
            'memory' => $this->faker->randomNumber(),
            'disk_space' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
