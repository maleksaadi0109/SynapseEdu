<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('course')) {
            $courseId = $this->route('course') instanceof Course
                ? $this->route('course')->id
                : $this->route('course');

            $this->merge([
                'course_id' => $this->input('course_id', $courseId),
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course') instanceof Course
            ? $this->route('course')
            : Course::find($this->route('course') ?? $this->input('course_id'));

        if (! $course) {
            return false;
        }

        return (bool) $this->user()?->can('create', [Assignment::class, $course]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'string', 'exists:courses,id'],
            'lesson_id' => ['nullable', 'string', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'due_date' => ['required', 'date', 'after:now'],
            'max_score' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
