<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum que agrupa las áreas temáticas principales de Desde Chipiona.
 */
enum ContentGroup: string implements HasColor, HasLabel
{
    case Historia = 'historia';
    case Monumentos = 'monumentos';
    case Playas = 'playas';
    case Fauna = 'fauna';
    case Flora = 'flora';
    case Gastronomia = 'gastronomia';
    case Personajes = 'personajes';
    case Fiestas = 'fiestas';
    case Refranes = 'refranes';

    /**
     * Obtener la etiqueta traducida del grupo temático.
     */
    public function label(): string
    {
        return match ($this) {
            self::Historia => __('filament.group_historia'),
            self::Monumentos => __('filament.group_monumentos'),
            self::Playas => __('filament.group_playas'),
            self::Fauna => __('filament.group_fauna'),
            self::Flora => __('filament.group_flora'),
            self::Gastronomia => __('filament.group_gastronomia'),
            self::Personajes => __('filament.group_personajes'),
            self::Fiestas => __('filament.group_fiestas'),
            self::Refranes => __('filament.group_refranes'),
        };
    }

    /**
     * Obtener la etiqueta para Filament.
     */
    public function getLabel(): ?string
    {
        return $this->label();
    }

    /**
     * Obtener el color semántico para Filament.
     */
    public function color(): string
    {
        return match ($this) {
            self::Historia => 'warning',
            self::Monumentos => 'info',
            self::Playas => 'info',
            self::Fauna, self::Flora => 'success',
            self::Gastronomia => 'danger',
            self::Personajes, self::Fiestas, self::Refranes => 'primary',
        };
    }

    /**
     * Obtener el color para contratos de Filament.
     */
    public function getColor(): string|array|null
    {
        return $this->color();
    }
}
