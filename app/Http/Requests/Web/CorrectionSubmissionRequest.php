<?php

namespace App\Http\Requests\Web;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * FormRequest para enviar una solicitud de corrección sobre una página o noticia.
 * Exige identificación OAuth verificada previa (Google / Facebook) o sesión de usuario.
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
     * Valida que el visitante esté autenticado mediante cuenta social o local.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($v): void {
            if (! $this->session()->has('public_social_identity') && ! auth()->check()) {
                $v->errors()->add('auth', 'Para proponer una corrección debes identificarte primero con tu cuenta de Google o Facebook.');
            }
        });
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
