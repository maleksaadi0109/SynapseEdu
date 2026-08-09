<?php

namespace App\Action;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCoursesAction
{
    /**
     * Retrieve courses using Laravel Scout full-text search with database driver.
     *
     * @param  string|null  $search  Search term entered by user
     * @param  string|null  $teacherId  Optional filter by teacher ULID
     * @param  int  $perPage  Number of items per page
     */
    public function handle(
        ?string $search = null,
        ?string $teacherId = null,
        ?bool $isPublic = true,
        int $perPage = 15
    ): LengthAwarePaginator {
        // If a search query is provided, execute Laravel Scout search
        if (! empty(trim((string) $search))) {
            return Course::search($search)
                ->query(function ($query) use ($teacherId, $isPublic) {
                    $query->with(['teacher.user'])
                        ->withCount('lessons')
                        ->when(! is_null($isPublic), fn ($q) => $q->where('is_public', $isPublic))
                        ->when($teacherId, fn ($q) => $q->where('teacher_id', $teacherId));
                })
                ->paginate($perPage);
        }

        // Standard Eloquent query when no search term is entered
        return Course::query()
            ->with(['teacher.user'])
            ->withCount('lessons')
            ->when(! is_null($isPublic), fn ($query) => $query->where('is_public', $isPublic))
            ->when($teacherId, fn ($query) => $query->where('teacher_id', $teacherId))
            ->latest()
            ->paginate($perPage);
    }
}
