<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controlador para el cambio de idioma del sitio web.
 * Persiste el idioma seleccionado en la sesión del usuario.
 */
class LanguageController extends Controller
{
    /**
     * Cambia el idioma activo de la aplicación y redirige a la página anterior verificada.
     * Solo acepta idiomas que estén en la lista de idiomas disponibles.
     *
     * @param  Request  $request  Petición HTTP entrante
     * @param  string  $locale  El código del idioma a establecer (ej: 'es', 'en')
     * @return RedirectResponse Redirección segura a la página de origen
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Validar que el idioma solicitado está disponible en la aplicación
        if (in_array($locale, config('locales.available', ['es', 'en']), true)) {
            session()->put('locale', $locale);
            app()->setLocale($locale);
        }

        $previousUrl = url()->previous(route('home'));
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $prevHost = parse_url($previousUrl, PHP_URL_HOST);
        $prevScheme = strtolower((string) parse_url($previousUrl, PHP_URL_SCHEME));

        if (
            str_starts_with($previousUrl, '//')
            || ($prevScheme !== '' && ! in_array($prevScheme, ['http', 'https'], true))
            || (! empty($prevHost) && $prevHost !== $appHost && $prevHost !== $request->getHost())
        ) {
            $previousUrl = route('home');
        }

        return redirect()->to($previousUrl)->withHeaders(['Content-Language' => app()->getLocale()]);
    }
}
