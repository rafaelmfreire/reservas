<?php

namespace App\Filament\Resources\ResponsibleResource\Pages;

use App\Filament\Resources\ResponsibleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResponsibles extends ListRecords
{
    protected static string $resource = ResponsibleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
