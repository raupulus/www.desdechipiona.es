<?php

namespace App\Filament\Admin\Resources\ContentSuggestionResource\Pages;

use App\Filament\Admin\Resources\ContentSuggestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentSuggestion extends EditRecord
{
    protected static string $resource = ContentSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
