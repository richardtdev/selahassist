#!/bin/bash

# SermonAssist SaaS Platform - Setup Test Data
# This script seeds the database with test data including subscription plans

# Exit on error
set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Banner
echo -e "${BLUE}===========================================================${NC}"
echo -e "${BLUE}       SermonAssist SaaS Platform - Setup Test Data        ${NC}"
echo -e "${BLUE}===========================================================${NC}"

# Check if we're in the Laravel project directory
if [ ! -f "artisan" ]; then
  echo -e "${RED}Error: Not in a Laravel project directory.${NC}"
  exit 1
fi

# Check if schema configuration exists
echo -e "${YELLOW}Verifying database schema configuration...${NC}"

# Check .env file
if ! grep -q "DB_SCHEMA" .env; then
  echo -e "${RED}Error: DB_SCHEMA not found in .env file.${NC}"
  echo -e "${YELLOW}Adding DB_SCHEMA=selah_assist to .env file...${NC}"
  echo "DB_SCHEMA=selah_assist" >> .env
  echo -e "${GREEN}Added DB_SCHEMA to .env file.${NC}"
fi

# Check database.php file
CONFIG_PATH="config/database.php"
if [ -f "$CONFIG_PATH" ]; then
  if ! grep -q "'schema' => env('DB_SCHEMA', 'public')" "$CONFIG_PATH"; then
    echo -e "${RED}Error: Schema configuration not found in database config.${NC}"
    echo -e "${YELLOW}Please run setup-sermonassist.sh first to set up the schema configuration.${NC}"
    exit 1
  else
    echo -e "${GREEN}Schema configuration found in database config.${NC}"
  fi
else
  echo -e "${RED}Error: Database config file not found.${NC}"
  exit 1
fi


# Check if models exist
if [ ! -f "app/Models/Plan.php" ] || [ ! -f "app/Models/SermonTemplate.php" ] || [ ! -f "app/Models/Sermon.php" ]; then
  echo -e "${RED}Error: Required models are missing.${NC}"
  echo -e "${YELLOW}Please run setup-sermonassist.sh first to create the necessary models.${NC}"
  exit 1
else
  echo -e "${GREEN}Required models found.${NC}"
fi

# Check if migrations exist
if [ ! -f "$(find database/migrations -name '*_create_selah_assist_schema.php')" ] || 
   [ ! -f "$(find database/migrations -name '*_create_plans_table.php')" ] || 
   [ ! -f "$(find database/migrations -name '*_create_sermon_templates_table.php')" ] || 
   [ ! -f "$(find database/migrations -name '*_create_sermons_table.php')" ]; then
  echo -e "${RED}Error: Required migrations are missing.${NC}"
  echo -e "${YELLOW}Please run setup-sermonassist.sh first to create the necessary migrations.${NC}"
  exit 1
else
  echo -e "${GREEN}Required migrations found.${NC}"
fi

# Create seeder files for plans
echo -e "${YELLOW}Creating seeder files...${NC}"

# Create Plans Table Seeder
mkdir -p database/seeders

cat > database/seeders/PlansTableSeeder.php << 'EOL'
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
EOL

# Create Role Table Seeder (for spatie/laravel-permission)
cat > database/seeders/RolesAndPermissionsSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Plan management
            'view plans',
            'create plans',
            'edit plans',
            'delete plans',
            
            // Sermon management
            'create sermons',
            'edit sermons',
            'delete sermons',
            'view all sermons',
            
            // Template management
            'create templates',
            'edit templates',
            'delete templates',
            'view all templates',
            
            // System management
            'access admin dashboard',
            'manage system settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $pastorRole = Role::create(['name' => 'pastor']);
        $pastorRole->givePermissionTo([
            'create sermons',
            'edit sermons',
            'delete sermons',
            'create templates',
            'edit templates',
            'delete templates',
        ]);

        // Assign roles to existing users
        $admin = User::where('email', 'admin@sermonassist.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }

        $pastor = User::where('email', 'pastor@sermonassist.com')->first();
        if ($pastor) {
            $pastor->assignRole('pastor');
        }
    }
}
EOL

# Create Users Table Seeder (with admin and test pastor)
cat > database/seeders/UsersTableSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sermonassist.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        
        // Create admin team
        $adminTeam = Team::forceCreate([
            'user_id' => $admin->id,
            'name' => 'Admin Team',
            'personal_team' => true,
        ]);
        
        $admin->current_team_id = $adminTeam->id;
        $admin->save();
        
        // Create test pastor user
        $pastor = User::create([
            'name' => 'Test Pastor',
            'email' => 'pastor@sermonassist.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);
        
        // Create pastor team
        $pastorTeam = Team::forceCreate([
            'user_id' => $pastor->id,
            'name' => 'Pastors Team',
            'personal_team' => true,
        ]);
        
        $pastor->current_team_id = $pastorTeam->id;
        $pastor->save();
    }
}
EOL

# Create Sermon Templates Seeder
cat > database/seeders/SermonTemplatesSeeder.php << 'EOL'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SermonTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sermon_templates')->insert([
            [
                'name' => 'Standard Sermon Template',
                'user_id' => null, // System template
                'structure' => json_encode([
                    'sections' => [
                        'introduction' => [
                            'title' => 'Introduction',
                            'description' => 'Begin with prayer, establish connection with audience, and introduce sermon topic.'
                        ],
                        'scripture_reading' => [
                            'title' => 'Scripture Reading',
                            'description' => 'Read the main scripture passage that will be the focus of the sermon.'
                        ],
                        'main_points' => [
                            'title' => 'Main Points',
                            'description' => 'Present 3-5 key points derived from the scripture.'
                        ],
                        'application' => [
                            'title' => 'Application',
                            'description' => 'How does this scripture apply to our lives today?'
                        ],
                        'conclusion' => [
                            'title' => 'Conclusion',
                            'description' => 'Summarize the message and end with a call to action.'
                        ],
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Expository Sermon Template',
                'user_id' => null, // System template
                'structure' => json_encode([
                    'sections' => [
                        'introduction' => [
                            'title' => 'Introduction',
                            'description' => 'Introduce the Bible passage and its context.'
                        ],
                        'historical_context' => [
                            'title' => 'Historical Context',
                            'description' => 'Explain the historical setting of the passage.'
                        ],
                        'verse_by_verse' => [
                            'title' => 'Verse by Verse Exposition',
                            'description' => 'Explain each verse in detail, drawing out meaning.'
                        ],
                        'theological_implications' => [
                            'title' => 'Theological Implications',
                            'description' => 'Discuss the theological principles in the passage.'
                        ],
                        'practical_application' => [
                            'title' => 'Practical Application',
                            'description' => 'How these truths should be applied in everyday life.'
                        ],
                        'conclusion' => [
                            'title' => 'Conclusion',
                            'description' => 'Summarize the main points and end with prayer.'
                        ],
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Topical Sermon Template',
                'user_id' => null, // System template
                'structure' => json_encode([
                    'sections' => [
                        'introduction' => [
                            'title' => 'Introduction',
                            'description' => 'Introduce the topic and its relevance.'
                        ],
                        'biblical_foundation' => [
                            'title' => 'Biblical Foundation',
                            'description' => 'Present key scriptures relevant to the topic.'
                        ],
                        'point_one' => [
                            'title' => 'First Major Point',
                            'description' => 'First aspect of the topic with supporting scriptures.'
                        ],
                        'point_two' => [
                            'title' => 'Second Major Point',
                            'description' => 'Second aspect of the topic with supporting scriptures.'
                        ],
                        'point_three' => [
                            'title' => 'Third Major Point',
                            'description' => 'Third aspect of the topic with supporting scriptures.'
                        ],
                        'practical_steps' => [
                            'title' => 'Practical Steps',
                            'description' => 'Specific actions listeners can take.'
                        ],
                        'conclusion' => [
                            'title' => 'Conclusion',
                            'description' => 'Summarize and call to action.'
                        ],
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
EOL

# Update the main DatabaseSeeder.php file
cat > database/seeders/DatabaseSeeder.php << 'EOL'
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
            PlansTableSeeder::class,
            UsersTableSeeder::class,
            SermonTemplatesSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
EOL

# Publish the spatie/laravel-permission migrations (if not already published)
echo -e "${YELLOW}Publishing spatie/laravel-permission migrations...${NC}"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --force

# Run migrations and seeds
echo -e "${YELLOW}Running migrations and seeding database...${NC}"
php artisan migrate
php artisan db:seed

echo -e "${BLUE}===========================================================${NC}"
echo -e "${GREEN}Test data setup completed successfully!${NC}"
echo -e "${BLUE}===========================================================${NC}"
echo -e "${YELLOW}Login credentials:${NC}"
echo -e "Admin: admin@sermonassist.com / password"
echo -e "Pastor: pastor@sermonassist.com / password"
echo -e "${BLUE}===========================================================${NC}"
