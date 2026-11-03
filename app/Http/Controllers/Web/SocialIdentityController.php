<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Controlador de identificación pública ligera (Facebook / Google) sin cuentas locales.
 * Guarda temporalmente la identidad verificada en sesión (`public_social_identity`)
 * para que el visitante pueda comentar, proponer correcciones, aportar contenido o subir refranes
 * sin necesidad de registrarse ni recordar contraseñas.
 */
class SocialIdentityController extends Controller
{
    /** @var array<int, string> Proveedores OAuth soportados */
    private const ALLOWED_PROVIDERS = ['facebook', 'google'];

    /**
     * Redirige al visitante al proveedor OAuth (Facebook o Google).
     * Si en desarrollo local las credenciales OAuth no están configuradas, permite un modo demostración seguro.
     */
    public function redirect(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        $previousUrl = url()->previous(route('home'));
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $prevHost = parse_url($previousUrl, PHP_URL_HOST);

        if (! empty($prevHost) && $prevHost !== $appHost && $prevHost !== $request->getHost()) {
            $previousUrl = route('home');
        }

        $request->session()->put('social_intended_url', $previousUrl);

        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        if (empty($clientId) || empty($clientSecret)) {
            if (! app()->environment(['local', 'testing'])) {
                abort(503, 'El servicio de autenticación social no está configurado.');
            }

            $request->session()->put('public_social_identity', [
                'provider' => $provider,
                'id' => 'local-'.$provider.'-visitor',
                'name' => $provider === 'facebook' ? 'Vecino de Chipiona (Facebook)' : 'Visitante Verificado (Google)',
                'email' => null,
                'avatar' => null,
            ]);

            return redirect()->to($this->safeIntendedUrl($request));
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Procesa la respuesta del proveedor OAuth y almacena la identidad pública en la sesión.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();

            $request->session()->put('public_social_identity', [
                'provider' => $provider,
                'id' => (string) $socialUser->getId(),
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Vecino de Chipiona',
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
            ]);
        } catch (Throwable $e) {
            Log::warning('Fallo en autenticación social con proveedor: '.$provider, [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->to($this->safeIntendedUrl($request))
                ->with('error', 'No se pudo completar la identificación con '.ucfirst($provider).'.');
        }

        return redirect()->to($this->safeIntendedUrl($request));
    }

    /**
     * Elimina la identidad social ligera de la sesión actual.
     */
    public function disconnect(Request $request): RedirectResponse
    {
        $request->session()->forget('public_social_identity');

        return redirect()->back(fallback: route('home'));
    }

    /**
     * Obtiene y valida la URL de retorno almacenada para prevenir Open Redirect.
     */
    private function safeIntendedUrl(Request $request): string
    {
        $url = (string) $request->session()->pull('social_intended_url', route('home'));
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);

        if (! empty($urlHost) && $urlHost !== $appHost && $urlHost !== $request->getHost()) {
            return route('home');
        }

        return $url;
    }
}
