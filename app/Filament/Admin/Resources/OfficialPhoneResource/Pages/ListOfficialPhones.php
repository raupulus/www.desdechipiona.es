<?php

namespace App\Filament\Admin\Resources\OfficialPhoneResource\Pages;

use App\Filament\Admin\Resources\OfficialPhoneResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfficialPhones extends ListRecords
{
    protected static string $resource = OfficialPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
