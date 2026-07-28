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
        Schema::create('students', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Foreign Key linking to users table (ULID)
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Academic Identifiers
            $table->string('student_number');
            $table->string('grade_level');
            $table->string('class_section')->nullable();
            $table->string('school_name')->nullable();

            // Personal & Guardian Contact
            $table->date('date_of_birth')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();

            // Offline Sync Versioning & Preferences
            $table->enum('sync_preference', ['wifi_only', 'any_network'])->default('wifi_only');
            $table->unsignedBigInteger('sync_version')->default(0);

            // AI Personalization Context
            $table->string('learning_preference')->default('textual');

            $table->timestamps();
            $table->softDeletes();

            // Performance & Delta Sync Indexes
            $table->unique(['student_number', 'school_name']);
            $table->index('updated_at');
            $table->index(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
