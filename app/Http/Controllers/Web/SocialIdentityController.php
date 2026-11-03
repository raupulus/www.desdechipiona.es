<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $request->session()->put('social_intended_url', url()->previous(route('home')));

        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        if (empty($clientId) || empty($clientSecret)) {
            $request->session()->put('public_social_identity', [
                'provider' => $provider,
                'id' => 'local-'.$provider.'-visitor',
                'name' => $provider === 'facebook' ? 'Vecino de Chipiona (Facebook)' : 'Visitante Verificado (Google)',
                'email' => null,
                'avatar' => null,
            ]);

            return redirect()->to($request->session()->pull('social_intended_url', route('home')));
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
        } catch (Throwable) {
            return redirect()
                ->to($request->session()->pull('social_intended_url', route('home')))
                ->with('error', 'No se pudo completar la identificación con '.ucfirst($provider).'.');
        }

        return redirect()->to($request->session()->pull('social_intended_url', route('home')));
    }

    /**
     * Elimina la identidad social ligera de la sesión actual.
     */
    public function disconnect(Request $request): RedirectResponse
    {
        $request->session()->forget('public_social_identity');

        return redirect()->back();
    }
}
