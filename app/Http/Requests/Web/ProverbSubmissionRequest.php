<?php

namespace App\Http\Requests\Web;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest para que un vecino o visitante suba un refrán al refranero de Chipiona.
 */
class ProverbSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:5', 'max:500'],
            'meaning' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', Rule::in(['mar', 'campo', 'vientos', 'fiestas', 'vida'])],
            'source_code' => ['nullable', 'string', 'max:150'],
            'related_page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_email' => ['nullable', 'email', 'max:191'],
            'cf-turnstile-response' => [new Turnstile],
        ];
    }
}
