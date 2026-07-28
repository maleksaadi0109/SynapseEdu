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
        Schema::create('essay_evaluations', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('submission_id')
                ->constrained('submissions')
                ->onDelete('cascade');

            $table->integer('ai_score')->nullable();
            $table->jsonb('rubric_breakdown')->nullable();
            $table->text('ai_feedback')->nullable();

            // Inter-rater Agreement (Fleiss' Kappa)
            $table->integer('human_score')->nullable();
            $table->float('kappa_score')->nullable();

            $table->timestamps();

            $table->index('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_evaluations');
    }
};
