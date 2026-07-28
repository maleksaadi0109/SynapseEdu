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
        Schema::create('submissions', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('assignment_id')
                ->constrained('assignments')
                ->onDelete('cascade');

            $table->foreignUlid('student_id')
                ->constrained('students')
                ->onDelete('cascade');

            $table->longText('content');
            $table->timestamp('submitted_at')->useCurrent();
            $table->enum('status', ['submitted', 'evaluating', 'graded'])->default('submitted');

            // Offline Sync Versioning
            $table->unsignedBigInteger('sync_version')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Performance & Delta Sync Indexes
            $table->index('updated_at');
            $table->index(['assignment_id', 'student_id']);
            $table->index(['student_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
