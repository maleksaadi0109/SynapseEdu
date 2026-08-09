<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Course extends Model
{
    use HasFactory;
    use HasUlids;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'code',
        'is_public',
        'sync_version',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $table = $this->belongsTo(Teacher::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
        ];
    }
}
