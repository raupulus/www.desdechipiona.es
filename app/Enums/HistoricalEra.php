<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum que define las grandes etapas históricas de Chipiona para el buscador y línea temporal.
 */
enum HistoricalEra: string implements HasColor, HasLabel
{
    case PrehistoryAntiquity = 'antigua';
    case MiddleAges = 'medieval';
    case EarlyModern = 'moderna';
    case Contemporary = 'contemporanea';
    case PresentNature = 'actual';

    /**
     * Obtener la etiqueta traducida de la época histórica.
     */
    public function label(): string
    {
        return match ($this) {
            self::PrehistoryAntiquity => __('filament.era_antigua'),
            self::MiddleAges => __('filament.era_medieval'),
            self::EarlyModern => __('filament.era_moderna'),
            self::Contemporary => __('filament.era_contemporanea'),
            self::PresentNature => __('filament.era_actual'),
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
            self::PrehistoryAntiquity => 'warning',
            self::MiddleAges => 'danger',
            self::EarlyModern => 'danger',
            self::Contemporary => 'primary',
            self::PresentNature => 'info',
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
