#!/bin/bash

# SermonAssist SaaS Platform - Initial Setup Script
# This script installs Laravel with Jetstream, Inertia, Vue, and other necessary packages
# for the core SermonAssist platform without AI integration

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
echo -e "${BLUE}       SermonAssist SaaS Platform - Initial DB SETUP          ${NC}"
echo -e "${BLUE}===========================================================${NC}"

# Create the schema migration if it doesn't exist
echo -e "${YELLOW}Creating schema migration...${NC}"
php artisan make:migration create_selah_assist_schema

# Find the migration file
SCHEMA_MIGRATION=$(find database/migrations -name '*_create_selah_assist_schema.php')

# Check if the migration file was found
if [ -z "$SCHEMA_MIGRATION" ]; then
    echo -e "${RED}Error: Schema migration file not found!${NC}"
    exit 1
fi

# Check if the file is writable
if [ ! -w "$SCHEMA_MIGRATION" ]; then
    echo -e "${RED}Error: Schema migration file is not writable!${NC}"
    exit 1
fi

# Update the migration file
cat > "$SCHEMA_MIGRATION" << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the schema if it doesn't exist
        DB::statement('CREATE SCHEMA IF NOT EXISTS selah_assist');
        
        // Set the search path to include our schema
        DB::statement('SET search_path TO selah_assist, public');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Don't drop the schema on rollback to prevent accidental data loss
        // Instead, just reset the search path
        DB::statement('SET search_path TO public');
    }
};
EOL

echo -e "${GREEN}Created schema migration file.${NC}"

# Create migration for plans table
echo -e "${YELLOW}Creating plans table migration...${NC}"
php artisan make:migration create_plans_table

# Find the migration file
PLANS_MIGRATION=$(find database/migrations -name '*_create_plans_table.php')

# Check if the migration file was found
if [ -z "$PLANS_MIGRATION" ]; then
    echo -e "${RED}Error: Plans migration file not found!${NC}"
    exit 1
fi

# Check if the file is writable
if [ ! -w "$PLANS_MIGRATION" ]; then
    echo -e "${RED}Error: Plans migration file is not writable!${NC}"
    exit 1
fi

# Update the migration file
cat > "$PLANS_MIGRATION" << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make sure we're using the correct schema
        $schema = config('database.connections.pgsql.schema', 'public');
        DB::statement("SET search_path TO {$schema}, public");
        
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_plan_id');
            $table->decimal('price', 8, 2);
            $table->integer('trial_days')->default(14);
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make sure we're using the correct schema
        $schema = config('database.connections.pgsql.schema', 'public');
        DB::statement("SET search_path TO {$schema}, public");
        
        Schema::dropIfExists('plans');
    }
};
EOL

# Create migration for sermon templates table
echo -e "${YELLOW}Creating sermon templates table migration...${NC}"
php artisan make:migration create_sermon_templates_table

# Find the migration file
TEMPLATES_MIGRATION=$(find database/migrations -name '*_create_sermon_templates_table.php')

# Check if the migration file was found
if [ -z "$TEMPLATES_MIGRATION" ]; then
    echo -e "${RED}Error: Sermon templates migration file not found!${NC}"
    exit 1
fi

# Check if the file is writable
if [ ! -w "$TEMPLATES_MIGRATION" ]; then
    echo -e "${RED}Error: Sermon templates migration file is not writable!${NC}"
    exit 1
fi

# Update the migration file
cat > "$TEMPLATES_MIGRATION" << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make sure we're using the correct schema
        $schema = config('database.connections.pgsql.schema', 'public');
        DB::statement("SET search_path TO {$schema}, public");
        
        Schema::create('sermon_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('user_id')->nullable(); // NULL for system templates
            $table->json('structure');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make sure we're using the correct schema
        $schema = config('database.connections.pgsql.schema', 'public');
        DB::statement("SET search_path TO {$schema}, public");
        
        Schema::dropIfExists('sermon_templates');
    }
};
EOL

# Create migration for sermons table
echo -e "${YELLOW}Creating sermons table migration...${NC}"
php artisan make:migration create_sermons_table

# Find the migration file
SERMONS_MIGRATION=$(find database/migrations -name '*_create_sermons_table.php')

# Check if the migration file was found
if [ -z "$SERMONS_MIGRATION" ]; then
    echo -e "${RED}Error: Sermons migration file not found!${NC}"
    exit 1
fi

# Check if the file is writable
if [ ! -w "$SERMONS_MIGRATION" ]; then
    echo -e "${RED}Error: Sermons migration file is not writable!${NC}"
    exit 1
fi

# Update the migration file
cat > "$SERMONS_MIGRATION" << 'EOL'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make sure we're using the correct schema
        $schema = config('database.connections.pgsql.schema', 'public');
        DB::statement("SET search_path TO {$schema}, public");
        
        Schema::create('sermons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sermon_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('scripture_reference')->nullable();
            $table->json('content');
            $table->boolean('is_draft')->default(true);
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Make sure we're using the correct schema
        $schema = config('database.connections.pgsql.schema', 'public');
        DB::statement("SET search_path TO {$schema}, public");
        
        Schema::dropIfExists('sermons');
    }
};
EOL

# Create Plan model
echo -e "${YELLOW}Creating Plan model...${NC}"
mkdir -p app/Models

cat > app/Models/Plan.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'stripe_plan_id',
        'price',
        'trial_days',
        'features',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'features' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Get all subscriptions for the plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
EOL

# Create SermonTemplate model
echo -e "${YELLOW}Creating SermonTemplate model...${NC}"

cat > app/Models/SermonTemplate.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SermonTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
        'structure',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'structure' => 'array',
    ];

    /**
     * Check if this is a system template.
     *
     * @return bool
     */
    public function isSystemTemplate()
    {
        return is_null($this->user_id);
    }

    /**
     * Get the user who created this template.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the sermons that use this template.
     */
    public function sermons()
    {
        return $this->hasMany(Sermon::class);
    }
}
EOL

# Create Sermon model
echo -e "${YELLOW}Creating Sermon model...${NC}"

cat > app/Models/Sermon.php << 'EOL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'sermon_template_id',
        'title',
        'scripture_reference',
        'content',
        'is_draft',
        'scheduled_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'is_draft' => 'boolean',
        'scheduled_date' => 'datetime',
    ];

    /**
     * Get the user who created the sermon.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team that the sermon belongs to.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the template that the sermon uses.
     */
    public function template()
    {
        return $this->belongsTo(SermonTemplate::class, 'sermon_template_id');
    }
}
EOL

echo -e "${GREEN}ALL PAU${NC}"