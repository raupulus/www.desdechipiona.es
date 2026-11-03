<?php

namespace App\Filament\Admin\Resources\OfficialPhoneResource\Pages;

use App\Filament\Admin\Resources\OfficialPhoneResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficialPhone extends EditRecord
{
    protected static string $resource = OfficialPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
