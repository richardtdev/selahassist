<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop the old user_id related index first
            // Cashier default index name for user_id and stripe_status
            $table->dropIndex('subscriptions_user_id_stripe_status_index'); 
            // Drop the user_id column
            $table->dropColumn('user_id');

            // Add team_id column
            $table->foreignId('team_id')->after('id')->constrained('teams')->onDelete('cascade');
            
            // Add new index for team_id and stripe_status
            $table->index(['team_id', 'stripe_status']); // Laravel will name it subscriptions_team_id_stripe_status_index
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop the team_id related index and column
            $table->dropIndex('subscriptions_team_id_stripe_status_index'); // Explicitly drop by generated name
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');

            // Re-add the user_id column (assuming it was unsignedBigInteger and nullable as typical for Cashier)
            // Note: Previous Cashier migrations make user_id unsignedBigInteger and not nullable.
            // We need to check the original Cashier migration for exact user_id definition.
            // For now, assuming unsignedBigInteger. The original Cashier migration for subscriptions has:
            // $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // However, since we are just adding the column back, we can't use constrained() directly here
            // if the users table itself wasn't modified or if we want to avoid re-adding constraint by name.
            // A simpler approach is to just add the column and its index.
            // Let's assume user_id was not nullable.
            $table->unsignedBigInteger('user_id'); // Re-add column
            // Re-add the index for user_id and stripe_status
            $table->index(['user_id', 'stripe_status']);
        });
    }
};
