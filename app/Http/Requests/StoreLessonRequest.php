<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Lesson;
use App\Models\Course;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course');
        if (!$course) {
            return false;
        }
        return $this->user()->can('create', [Lesson::class,$course]) ?? false ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'string','exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'order_index' => ['nullable', 'integer', 'min:0'],

        ];
    }
}
