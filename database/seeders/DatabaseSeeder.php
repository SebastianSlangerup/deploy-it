<?php

namespace Database\Seeders;

use App\Models\Dependency;
use App\Models\Template;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Sebastian Møller',
            'email' => 'seba7271@edu.sde.dk',
            'password' => Hash::make('password'),
        ]);

        //Template::factory()
        //    ->has(
        //        Dependency::factory()->count(5)
        //    )
        //    ->count(3)
        //    ->create();
    }
}
