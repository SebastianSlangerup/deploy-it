<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Container;
use App\Models\Instance;
use App\Models\Server;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        Server::factory()->count(5)->create()->each(function (Server $server) {
            Instance::factory()->create([
                'instanceable_id' => $server->id,
                'instanceable_type' => Server::class,
            ]);
        });

        Container::factory()->count(5)->create()->each(function (Container $container) {
            Instance::factory()->create([
                'instanceable_id' => $container->id,
                'instanceable_type' => Container::class,
            ]);
        });
    }
}
