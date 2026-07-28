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
        Schema::create('lessons', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('course_id')
                ->constrained('courses')
                ->onDelete('cascade');

            $table->string('title');
            $table->longText('content')->nullable();
            $table->integer('order_index')->default(0);

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
        Schema::dropIfExists('lessons');
    }
};
