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

        $seo->setTitle(__('messages.phones_nav').' — '.setting('site_title', config('app.name')))
            ->setDescription(__('messages.phones_seo_description'))
            ->setUrl(route('phones.index'))
            ->setBreadcrumbs([
                ['name' => __('messages.home'), 'url' => route('home')],
                ['name' => __('messages.phones_nav'), 'url' => route('phones.index')],
            ]);

        return view('phones.index', compact('phones', 'category', 'search', 'seo'));
    }
}
