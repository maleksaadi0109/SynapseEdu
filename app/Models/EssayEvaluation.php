<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssayEvaluation extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'submission_id',
        'ai_score',
        'rubric_breakdown',
        'ai_feedback',
        'human_score',
        'kappa_score',
    ];

    protected $casts = [
        'rubric_breakdown' => 'array',
        'kappa_score' => 'float',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
