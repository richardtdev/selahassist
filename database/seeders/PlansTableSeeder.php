<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            [
                'name' => 'Standard Plan',
                'slug' => 'standard',
                'stripe_plan_id' => 'price_standard',
                'price' => 20.00,
                'trial_days' => 14,
                'features' => json_encode([
                    'sermons_per_month' => 10,
                    'sermon_templates' => true,
                    'bible_translations' => ['KJV', 'NKJV', 'NIV'],
                    'news_summaries' => true,
                ]),
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Premium Plan',
                'slug' => 'premium',
                'stripe_plan_id' => 'price_premium',
                'price' => 30.00,
                'trial_days' => 14,
                'features' => json_encode([
                    'sermons_per_month' => 30,
                    'sermon_templates' => true,
                    'bible_translations' => ['KJV', 'NKJV', 'NIV', 'ESV', 'NASB', 'Hebrew'],
                    'news_summaries' => true,
                    'advanced_features' => true,
                    'export_options' => true,
                ]),
                'active' => false, // Not active for MVP
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
