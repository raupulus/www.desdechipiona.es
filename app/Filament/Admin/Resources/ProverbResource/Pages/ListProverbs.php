<?php

namespace App\Filament\Admin\Resources\ProverbResource\Pages;

use App\Filament\Admin\Resources\ProverbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProverbs extends ListRecords
{
    protected static string $resource = ProverbResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
