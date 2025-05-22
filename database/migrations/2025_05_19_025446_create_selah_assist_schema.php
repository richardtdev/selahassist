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
