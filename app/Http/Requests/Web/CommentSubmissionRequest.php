<?php

namespace App\Http\Requests\Web;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest para enviar un comentario público en una página o noticia.
 */
class CommentSubmissionRequest extends FormRequest
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
            'commentable_type' => ['required', 'string', Rule::in(['page', 'news'])],
            'commentable_id' => ['required', 'integer', 'min:1'],
            'body' => ['required', 'string', 'min:5', 'max:2500'],
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
