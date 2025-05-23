<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PlanSeeder::class, // Added the new PlanSeeder
            PlansTableSeeder::class,
            UsersTableSeeder::class,
            SermonTemplatesSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
