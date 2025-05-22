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
