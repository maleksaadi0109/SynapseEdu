<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable pgvector extension for PostgreSQL
        if (config('database.default') === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');
        }

        Schema::create('document_chunks', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('lesson_id')
                ->constrained('lessons')
                ->onDelete('cascade');

            $table->text('chunk_content');
            $table->jsonb('metadata')->nullable();

            $table->timestamps();

            $table->index('lesson_id');
        });

        // Add vector column for PostgreSQL pgvector
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE document_chunks ADD COLUMN embedding vector(1536);');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
