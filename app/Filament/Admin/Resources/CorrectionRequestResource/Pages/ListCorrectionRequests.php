<?php

namespace App\Filament\Admin\Resources\CorrectionRequestResource\Pages;

use App\Filament\Admin\Resources\CorrectionRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCorrectionRequests extends ListRecords
{
    protected static string $resource = CorrectionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
