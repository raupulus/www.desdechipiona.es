<?php

namespace App\Filament\Admin\Resources\CorrectionRequestResource\Pages;

use App\Filament\Admin\Resources\CorrectionRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCorrectionRequest extends EditRecord
{
    protected static string $resource = CorrectionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
