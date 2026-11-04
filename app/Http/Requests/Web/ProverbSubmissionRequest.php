<?php

namespace App\Http\Requests\Web;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * FormRequest para que un vecino o visitante suba un refrán al refranero de Chipiona.
 * Exige identificación OAuth verificada previa (Google / Facebook) o sesión de usuario,
 * moderación previa y bloqueo de URLs spam.
 */
class ProverbSubmissionRequest extends FormRequest
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
            'text' => [
                'required',
                'string',
                'min:5',
                'max:500',
                'regex:/^((?!https?:\/\/|www\.|\[url|<a\s).)*$/is',
            ],
            'meaning' => [
                'nullable',
                'string',
                'max:2000',
                'regex:/^((?!https?:\/\/|www\.|\[url|<a\s).)*$/is',
            ],
            'category' => ['required', 'string', Rule::in(['mar', 'campo', 'vientos', 'fiestas', 'vida'])],
            'source_code' => ['nullable', 'string', 'max:150'],
            'related_page_id' => ['nullable', 'integer', 'exists:pages,id'],
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
                $v->errors()->add('auth', 'Para subir un refrán debes identificarte primero con tu cuenta de Google o Facebook.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.regex' => 'Por motivos de seguridad y prevención de spam, los refranes no pueden contener enlaces web ni direcciones URL.',
            'meaning.regex' => 'Por motivos de seguridad y prevención de spam, el significado no puede contener enlaces web ni direcciones URL.',
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
