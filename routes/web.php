<?php

use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\ContentSuggestionController;
use App\Http\Controllers\Web\CorrectionRequestController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\LogDownloadController;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\OfficialPhoneController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\ProverbController;
use App\Http\Controllers\Web\SocialIdentityController;
use App\Http\Controllers\Web\StaticPageController;
use App\Http\Controllers\Web\WeatherController;
use Illuminate\Support\Facades\Route;

// --- Página principal y Explorador Multicriterio de Chipiona ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [HomeController::class, 'index'])->name('explore');
Route::permanentRedirect('/explorar', '/');

// --- Blog / Artículos (Módulo mantenido en código, desactivado temporalmente en frontend) ---
// Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
// Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');

// --- Noticias independientes (CRUD separado de páginas) ---
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// --- Páginas enciclopédicas y patrimoniales ---
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// --- Refranero popular de Chipiona ---
Route::get('/proverbs', [ProverbController::class, 'index'])->name('proverbs.index');
Route::post('/proverbs', [ProverbController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('proverbs.store');

// --- Participación ciudadana: Sugerir contenidos, Correcciones y Comentarios ---
Route::get('/suggestions', [ContentSuggestionController::class, 'create'])->name('suggestions.create');
Route::post('/suggestions', [ContentSuggestionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('suggestions.store');

Route::post('/corrections', [CorrectionRequestController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('corrections.store');

Route::post('/comments', [CommentController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('comments.store');

// --- Identificación pública ligera con Facebook / Google (sin cuentas en plataforma) ---
Route::post('/auth/social/disconnect', [SocialIdentityController::class, 'disconnect'])
    ->middleware('throttle:15,1')
    ->name('social.disconnect');
Route::get('/auth/social/{provider}/redirect', [SocialIdentityController::class, 'redirect'])
    ->middleware('throttle:15,1')
    ->name('social.redirect');
Route::get('/auth/social/{provider}/callback', [SocialIdentityController::class, 'callback'])
    ->middleware('throttle:15,1')
    ->name('social.callback');

// --- Utilidades de Chipiona: El Tiempo y Mareas (AEMET) + Teléfonos Oficiales ---
Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::get('/phones', [OfficialPhoneController::class, 'index'])->name('phones.index');

// --- Contacto ---
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('throttle:10,1')
    ->name('contact.send');

// --- Páginas institucionales y legales ---
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/legal', [StaticPageController::class, 'legal'])->name('legal');
Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/cookies', [StaticPageController::class, 'cookies'])->name('cookies');
Route::get('/ads.txt', [StaticPageController::class, 'adsTxt'])->name('ads.txt');

// --- Cambio de idioma ---
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// --- Descarga de logs (solo admin) ---
Route::get('/admin/logs/download/{filename}', [LogDownloadController::class, 'download'])
    ->middleware(['auth'])
    ->name('admin.logs.download');

// --- Redirección unificada al panel único de administración (/admin) ---
Route::redirect('/panel', '/admin');
Route::redirect('/panel/{any}', '/admin')->where('any', '.*');

// --- Autenticación (Breeze para intranet /admin) ---
require __DIR__.'/auth.php';
