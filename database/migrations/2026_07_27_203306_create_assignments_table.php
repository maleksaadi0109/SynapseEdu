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
        Schema::create('assignments', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('lesson_id')
                ->nullable()
                ->constrained('lessons')
                ->onDelete('set null');

            $table->foreignUlid('course_id')
                ->constrained('courses')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('instructions')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->integer('max_score')->default(100);

            // Offline Sync Versioning
            $table->unsignedBigInteger('sync_version')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Performance & Delta Sync Indexes
            $table->index('updated_at');
            $table->index(['course_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
