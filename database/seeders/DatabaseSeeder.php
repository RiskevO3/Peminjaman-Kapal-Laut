<?php

namespace Database\Seeders;

use App\Models\Ship;
use App\Models\ShipCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users
        User::factory(10)->create();

        // Seed ship categories
        ShipCategory::factory(5)->create();

        // Seed ships
        Ship::factory(10)->create();
    }
}