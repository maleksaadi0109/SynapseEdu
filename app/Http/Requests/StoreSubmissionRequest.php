<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $assignment = $this->route('assignment') instanceof Assignment
            ? $this->route('assignment')
            : Assignment::find($this->route('assignment') ?? $this->input('assignment_id'));

        if (! $assignment) {
            return false;
        }

        return (bool) $this->user()?->can('create', [Submission::class, $assignment]);
    }

    /**
     * Merge route parameter before validation.
     */
    public function prepareForValidation(): void
    {
        if ($this->route('assignment')) {
            $assignmentId = $this->route('assignment') instanceof Assignment
                ? $this->route('assignment')->id
                : $this->route('assignment');

            $this->merge([
                'assignment_id' => $this->input('assignment_id', $assignmentId),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'string', 'exists:assignments,id'],
            'content' => ['required', 'string', 'min:5'],
        ];
    }
}
