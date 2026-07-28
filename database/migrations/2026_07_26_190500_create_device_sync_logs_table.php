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
        Schema::create('device_sync_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Link to users table
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('device_id');
            $table->timestamp('last_synced_at');

            $table->timestamps();

            // Composite unique constraint (1 record per user per device)
            $table->unique(['user_id', 'device_id']);

            // Index for querying recent device syncs
            $table->index(['user_id', 'last_synced_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_sync_logs');
    }
};
