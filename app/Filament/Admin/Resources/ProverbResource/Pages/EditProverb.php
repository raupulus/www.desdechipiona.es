<?php

namespace App\Filament\Admin\Resources\ProverbResource\Pages;

use App\Filament\Admin\Resources\ProverbResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProverb extends EditRecord
{
    protected static string $resource = ProverbResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
