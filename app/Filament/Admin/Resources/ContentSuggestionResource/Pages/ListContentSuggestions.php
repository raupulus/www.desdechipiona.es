<?php

namespace App\Filament\Admin\Resources\ContentSuggestionResource\Pages;

use App\Filament\Admin\Resources\ContentSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentSuggestions extends ListRecords
{
    protected static string $resource = ContentSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
