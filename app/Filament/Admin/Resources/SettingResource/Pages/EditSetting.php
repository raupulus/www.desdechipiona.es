<?php

namespace App\Filament\Admin\Resources\SettingResource\Pages;

use App\Filament\Admin\Resources\SettingResource;
use App\Services\SettingService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['value']) && is_array($data['value'])) {
            $first = reset($data['value']);
            $data['value'] = is_string($first) ? $first : null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        app(SettingService::class)->clearCache();
    }
}
