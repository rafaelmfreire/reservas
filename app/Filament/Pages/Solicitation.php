<?php

namespace App\Filament\Pages;

use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class Solicitation extends SimplePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.solicitation';
    protected static ?string $title = 'Solicitar';

    public ?string $selectedSector = null;

    public function getTitle(): string|Htmlable
    {
        return parent::getTitle() . ' Sala ao ' . strtoupper($this->selectedSector);
    }

    public function mount($sector)
    {
        $this->selectedSector = $sector;
    }
}
