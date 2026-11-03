<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource para el modelo Page.
 * Oculta claves foráneas internas (user_id) y marcas de borrado lógico (deleted_at).
 */
class PageResource extends JsonResource
{
    /**
     * Transforma el recurso en un array para la respuesta JSON.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'social_image' => $this->social_image,
            'social_image_url' => $this->social_image_url,
            'youtube_url' => $this->youtube_url,
            'content_group' => $this->content_group,
            'historical_era' => $this->historical_era,
            'period_label' => $this->period_label,
            'year_start' => $this->year_start,
            'year_end' => $this->year_end,
            'evidence_level' => $this->evidence_level,
            'evidence_note' => $this->evidence_note,
            'source_references' => $this->source_references,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_name' => $this->location_name,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'related_pages' => PageResource::collection($this->whenLoaded('relatedPages')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
