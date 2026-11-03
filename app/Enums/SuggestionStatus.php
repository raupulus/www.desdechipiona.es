<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Enum que define los estados de revisión de solicitudes de corrección, sugerencias y refranes.
 */
enum SuggestionStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    /**
     * Obtener la etiqueta traducida del estado.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => __('filament.suggestion_pending'),
            self::Approved => __('filament.suggestion_approved'),
            self::Rejected => __('filament.suggestion_rejected'),
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
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
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
