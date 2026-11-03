<?php

namespace App\Http\Requests\Web;

use App\Enums\ContentGroup;
use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * FormRequest para el formulario ciudadano de sugerencia de nuevos contenidos.
 */
class ContentSuggestionSubmissionRequest extends FormRequest
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
            'content_group' => ['required', new Enum(ContentGroup::class)],
            'title' => ['required', 'string', 'min:4', 'max:255'],
            'related_page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'body' => ['required', 'string', 'min:20', 'max:15000'],
            'sources_info' => ['nullable', 'string', 'max:3000'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_email' => ['nullable', 'email', 'max:191'],
            'cf-turnstile-response' => [new Turnstile],
        ];
    }
}
