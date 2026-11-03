<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Regla de validación para Cloudflare Turnstile.
 * Si las claves no están definidas en el archivo .env (desarrollo local y tests),
 * omite automáticamente la verificación externa.
 */
class Turnstile implements ValidationRule
{
    /**
     * Indica que la regla debe evaluarse aunque el atributo no esté presente o esté vacío.
     */
    public bool $implicit = true;

    /**
     * @param  string|null  $expectedAction  Acción esperada del widget Turnstile (opcional)
     */
    public function __construct(
        protected ?string $expectedAction = null
    ) {}

    /**
     * Ejecuta la validación del token cf-turnstile-response contra Cloudflare.
     *
     * @param  string  $attribute  Nombre del campo validado
     * @param  mixed  $value  Token enviado por el widget Turnstile
     * @param  Closure  $fail  Callback de error de validación
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $siteKey = config('services.turnstile.site_key');
        $secretKey = config('services.turnstile.secret_key');

        if (empty($siteKey) || empty($secretKey)) {
            return;
        }

        if (empty($value) || ! is_string($value)) {
            $fail(__('messages.turnstile_required'));

            return;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            );

            if (! $response->successful() || ! ($response->json('success') ?? false)) {
                $fail(__('messages.turnstile_failed'));

                return;
            }

            if ($this->expectedAction !== null) {
                $returnedAction = $response->json('action');
                if ($returnedAction !== null && $returnedAction !== $this->expectedAction) {
                    $fail(__('messages.turnstile_failed'));
                }
            }
        } catch (Throwable $e) {
            Log::warning('Error comunicando con el validador de Cloudflare Turnstile', [
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            if (app()->isProduction()) {
                $fail(__('messages.turnstile_failed'));
            }
        }
    }
}
