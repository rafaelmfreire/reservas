<?php

namespace App\Filament\Resources\ResponsibleResource\Pages;

use App\Filament\Resources\ResponsibleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResponsible extends EditRecord
{
    protected static string $resource = ResponsibleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
