<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OfficialPhone;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controlador público para el directorio de Teléfonos Oficiales de Chipiona.
 */
class OfficialPhoneController extends Controller
{
    /**
     * Muestra los teléfonos de información y emergencias oficiales de Chipiona.
     */
    public function index(Request $request, SeoService $seo): View
    {
        $category = (string) $request->query('category', '');
        $search = trim((string) $request->query('q', ''));

        $query = OfficialPhone::active()->ordered();

        if ($category !== '' && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $likeOp = $query->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($sub) use ($likeOp, $term): void {
                $sub->where('name', $likeOp, $term)
                    ->orWhere('phone', $likeOp, $term)
                    ->orWhere('description', $likeOp, $term)
                    ->orWhere('address', $likeOp, $term);
            });
        }

        $phones = $query->get();
        $emergencyPhones = OfficialPhone::active()->where('is_emergency', true)->ordered()->get();

        $seo->setTitle('Teléfonos Oficiales y Urgencias de Chipiona - '.setting('site_title', config('app.name')))
            ->setDescription('Directorio actualizado de teléfonos de información oficial, urgencias médicas, Policía Local, Guardia Civil y servicios municipales de Chipiona.')
            ->setUrl(route('phones.index'));

        return view('phones.index', compact('phones', 'emergencyPhones', 'category', 'search', 'seo'));
    }
}
