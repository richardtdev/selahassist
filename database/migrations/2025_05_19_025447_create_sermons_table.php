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
