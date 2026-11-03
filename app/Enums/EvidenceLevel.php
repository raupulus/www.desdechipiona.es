<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum que define el nivel de evidencia y rigor documental de cada contenido.
 * Permite separar claramente información contrastada en archivo frente a mitos/leyendas o hipótesis.
 */
enum EvidenceLevel: string implements HasColor, HasLabel
{
    case Consolidated = 'consolidado';
    case Documented = 'documentado';
    case Tradition = 'tradicion';
    case Disputed = 'discutido';

    /**
     * Obtener la etiqueta traducida del nivel de evidencia.
     */
    public function label(): string
    {
        return match ($this) {
            self::Consolidated => __('filament.evidence_consolidated'),
            self::Documented => __('filament.evidence_documented'),
            self::Tradition => __('filament.evidence_tradition'),
            self::Disputed => __('filament.evidence_disputed'),
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
            self::Consolidated => 'info',
            self::Documented => 'primary',
            self::Tradition => 'warning',
            self::Disputed => 'danger',
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
