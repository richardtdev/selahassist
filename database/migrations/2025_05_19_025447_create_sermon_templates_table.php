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
