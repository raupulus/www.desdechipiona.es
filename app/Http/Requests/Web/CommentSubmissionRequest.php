<?php

namespace App\Http\Requests\Web;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * FormRequest para enviar un comentario en una página o noticia.
 * Exige identificación OAuth verificada previa (Google / Facebook) o sesión de usuario,
 * moderación obligatoria y bloqueo estricto contra spam y URLs.
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
            'body' => [
                'required',
                'string',
                'min:5',
                'max:2500',
                'regex:/^((?!https?:\/\/|www\.|\[url|<a\s).)*$/is',
            ],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_email' => ['nullable', 'email', 'max:191'],
            'privacy' => ['required', 'accepted'],
            'cf-turnstile-response' => [new Turnstile],
        ];
    }

    /**
     * Valida que el visitante esté autenticado mediante cuenta social o local.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v): void {
            if (! $this->session()->has('public_social_identity') && ! auth()->check()) {
                $v->errors()->add('auth', 'Para enviar un comentario debes identificarte primero con tu cuenta de Google o Facebook.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.regex' => 'Por motivos de seguridad y prevención de spam, los comentarios no pueden contener enlaces web ni direcciones URL.',
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
