<?php

namespace App\Filament\Admin\Resources\SettingResource\Pages;

use App\Filament\Admin\Resources\SettingResource;
use App\Services\SettingService;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['value']) && is_array($data['value'])) {
            $first = reset($data['value']);
            $data['value'] = is_string($first) ? $first : null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        app(SettingService::class)->clearCache();
    }
}
