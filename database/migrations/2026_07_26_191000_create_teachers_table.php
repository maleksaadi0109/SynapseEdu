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
        Schema::create('teachers', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Link to users table (ULID)
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Professional Details
            $table->string('teacher_number');
            $table->string('department')->nullable();
            $table->string('specialization')->nullable();
            $table->string('qualification')->nullable();
            $table->string('school_name')->nullable();
            $table->text('bio')->nullable();

            // Offline Sync Versioning
            $table->unsignedBigInteger('sync_version')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Uniqueness & Delta Sync Indexes
            $table->unique(['teacher_number', 'school_name']);
            $table->index('updated_at');
            $table->index(['user_id', 'deleted_at']);
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
