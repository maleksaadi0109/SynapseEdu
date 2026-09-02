<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $submission = $this->route('submission') instanceof Submission
            ? $this->route('submission')
            : Submission::find($this->route('submission'));

        if (! $submission) {
            return false;
        }

        return (bool) $this->user()?->can('update', $submission);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:5'],
        ];
    }
}
