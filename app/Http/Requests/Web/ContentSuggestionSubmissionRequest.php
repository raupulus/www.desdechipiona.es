<?php

namespace App\Http\Requests\Web;

use App\Enums\ContentGroup;
use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

/**
 * FormRequest para el formulario ciudadano de sugerencia de nuevos contenidos.
 * Exige identificación OAuth verificada previa (Google / Facebook) o sesión de usuario.
 */
class ContentSuggestionSubmissionRequest extends FormRequest
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
                $v->errors()->add('auth', 'Para enviar una sugerencia o aportación debes identificarte primero con tu cuenta de Google o Facebook.');
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
