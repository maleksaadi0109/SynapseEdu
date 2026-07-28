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
        Schema::create('notes', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('body')->nullable();

            // Offline Sync Versioning
            $table->unsignedBigInteger('sync_version')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Performance & Delta Sync Indexes
            $table->index('updated_at');
            $table->index(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
