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
        Schema::create('courses', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('teacher_id')
                ->constrained('teachers')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('code')->nullable();
            $table->boolean('is_public')->default(true);

            // Offline Sync Versioning
            $table->unsignedBigInteger('sync_version')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Performance & Delta Sync Indexes
            $table->index('updated_at');
            $table->index(['teacher_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
