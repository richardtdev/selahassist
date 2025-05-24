<?php

namespace Database\Seeders;

use App\Models\Plan; // Import the Plan model
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::updateOrCreate(
            ['slug' => 'standard'], // Check by slug to avoid duplicates
            [
                'name' => 'Standard',
                'stripe_plan_id' => 'price_standard_monthly', // Placeholder
                'price' => 20.00,
                'trial_days' => 14,
                'features' => ['max_users' => 2, 'description' => 'Standard features for small teams.'],
                'active' => true,
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'premium'], // Check by slug to avoid duplicates
            [
                'name' => 'Premium',
                'stripe_plan_id' => 'price_premium_monthly', // Placeholder
                'price' => 30.00,
                'trial_days' => 14,
                'features' => ['max_users' => 5, 'description' => 'Premium features for growing teams.'],
                'active' => true,
            ]
        );
    }
}
