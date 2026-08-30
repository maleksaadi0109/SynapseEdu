<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $assignment = $this->route('assignment') instanceof Assignment
            ? $this->route('assignment')
            : Assignment::find($this->route('assignment'));

        if (! $assignment) {
            return false;
        }

        return (bool) $this->user()?->can('update', $assignment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lesson_id' => ['nullable', 'string', 'exists:lessons,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'due_date' => ['sometimes', 'required', 'date'],
            'max_score' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
