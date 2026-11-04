<?php

namespace App\Services;

use App\Enums\ContentGroup;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Models\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Servicio central de búsqueda y filtrado multicriterio de los contenidos de Desde Chipiona.
 * Permite buscar por texto libre, apartado temático (content_group), época histórica (historical_era),
 * rango de años (year_from / year_to), nivel de rigor documental (evidence_level), categoría y coordenadas GPS.
 */
class ContentSearchService
{
    /**
     * Filtra y pagina las páginas enciclopédicas publicadas según los criterios del buscador.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Page>
     */
    public function searchPages(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Page::publishedAndActive()->with(['categories', 'relatedPages' => fn ($q) => $q->publishedAndActive()]);

        $this->applyFilters($query, $filters);

        $sort = (string) ($filters['sort'] ?? 'featured');

        if ($sort === 'chronological_asc') {
            $query->orderByRaw('CASE WHEN year_start IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('year_start', 'asc')
                ->orderByDesc('published_at');
        } elseif ($sort === 'chronological_desc') {
            $query->orderByRaw('CASE WHEN year_start IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('year_start', 'desc')
                ->orderByDesc('published_at');
        } elseif ($sort === 'recent') {
            $query->orderByDesc('published_at')->orderByDesc('id');
        } else {
            $query->orderByDesc('featured')
                ->orderByRaw('CASE WHEN year_start IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('year_start', 'asc')
                ->orderByDesc('published_at');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Devuelve los conteos por grupo temático para mostrar en las píldoras del explorador.
     *
     * @return array<string, int>
     */
    public function getGroupCounts(): array
    {
        $counts = Page::publishedAndActive()
            ->selectRaw('content_group, COUNT(*) as total')
            ->groupBy('content_group')
            ->pluck('total', 'content_group')
            ->all();

        $result = ['all' => array_sum($counts)];
        foreach (ContentGroup::cases() as $group) {
            $result[$group->value] = (int) ($counts[$group->value] ?? 0);
        }

        return $result;
    }

    /**
     * Devuelve todas las páginas con coordenadas GPS válidas para el mapa interactivo.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Page>
     */
    public function getGeolocatedPages(array $filters = []): Collection
    {
        $query = Page::publishedAndActive()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $this->applyFilters($query, $filters);

        return $query->limit(60)->get();
    }

    /**
     * Aplica los filtros sobre el query builder de Page.
     *
     * @param  Builder<Page>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        $search = mb_substr(trim((string) ($filters['q'] ?? '')), 0, 120);
        if ($search !== '') {
            $isPgsql = $query->getConnection()->getDriverName() === 'pgsql';
            $likeOp = $isPgsql ? 'ILIKE' : 'LIKE';
            $term = '%'.addcslashes($search, '%_\\').'%';

            $query->where(function (Builder $sub) use ($isPgsql, $likeOp, $term): void {
                if ($isPgsql) {
                    $sub->whereRaw('title::text ILIKE ?', [$term])
                        ->orWhereRaw('excerpt::text ILIKE ?', [$term])
                        ->orWhereRaw('content::text ILIKE ?', [$term])
                        ->orWhere('location_name', $likeOp, $term)
                        ->orWhere('period_label', $likeOp, $term)
                        ->orWhereRaw('source_references::text ILIKE ?', [$term]);
                } else {
                    $sub->where('title', $likeOp, $term)
                        ->orWhere('excerpt', $likeOp, $term)
                        ->orWhere('content', $likeOp, $term)
                        ->orWhere('location_name', $likeOp, $term)
                        ->orWhere('period_label', $likeOp, $term)
                        ->orWhere('source_references', $likeOp, $term);
                }
            });
        }

        $group = (string) ($filters['group'] ?? '');
        if ($group !== '' && $group !== 'all' && ContentGroup::tryFrom($group) !== null) {
            $query->where('content_group', $group);
        }

        $era = (string) ($filters['era'] ?? '');
        if ($era !== '' && $era !== 'all' && HistoricalEra::tryFrom($era) !== null) {
            $query->where('historical_era', $era);
        }

        $evidence = (string) ($filters['evidence'] ?? '');
        if ($evidence !== '' && $evidence !== 'all' && EvidenceLevel::tryFrom($evidence) !== null) {
            $query->where('evidence_level', $evidence);
        }

        $categorySlug = trim((string) ($filters['category'] ?? ''));
        if ($categorySlug !== '') {
            $query->whereHas('categories', fn (Builder $q) => $q->where('slug', $categorySlug));
        }

        if (isset($filters['year_from']) && $filters['year_from'] !== '' && is_numeric($filters['year_from'])) {
            $yearFrom = max(-32000, min(32000, (int) $filters['year_from']));
            $query->where(function (Builder $q) use ($yearFrom): void {
                $q->where('year_start', '>=', $yearFrom)
                    ->orWhere('year_end', '>=', $yearFrom);
            });
        }

        if (isset($filters['year_to']) && $filters['year_to'] !== '' && is_numeric($filters['year_to'])) {
            $yearTo = max(-32000, min(32000, (int) $filters['year_to']));
            $query->where('year_start', '<=', $yearTo);
        }

        if (! empty($filters['map'])) {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }
    }
}
