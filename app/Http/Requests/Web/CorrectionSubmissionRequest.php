<?php

namespace App\Http\Requests\Web;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest para enviar una solicitud de corrección sobre una página o noticia.
 */
class CorrectionSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('privacy_accepted') && ! $this->has('privacy')) {
            $this->merge(['privacy' => $this->input('privacy_accepted')]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'correctable_type' => ['required', 'string', Rule::in(['page', 'news'])],
            'correctable_id' => ['required', 'integer', 'min:1'],
            'quoted_text' => ['nullable', 'string', 'max:1000'],
            'proposed_correction' => ['required', 'string', 'min:10', 'max:5000'],
            'sources_info' => ['nullable', 'string', 'max:2000'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_email' => ['nullable', 'email', 'max:191'],
            'privacy' => ['required', 'accepted'],
            'cf-turnstile-response' => [new Turnstile],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'privacy' => 'política de privacidad',
        ];
    }
}
