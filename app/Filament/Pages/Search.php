<?php

namespace App\Filament\Pages;

use App\Models\Room;
use App\Models\User;
use Filament\Pages\SimplePage;

class Search extends SimplePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.search';
    protected static ?string $title = 'Consultar';

    public array $sectors;
    public array $rooms;
    public ?string $selectedSector = null;
    public ?string $selectedRoom = null;

    public function mount($sector)
    {
        $this->sectors = User::where('is_admin', false)->pluck('name', 'id')->toArray();

        $this->selectedSector = $sector;
        $sectorId = User::where('name', $sector)->where('is_admin', false)->first()?->id;

        if (is_null($sectorId)) {
            abort(404, 'Página não encontrada.');
        }

        $this->rooms = Room::where('user_id', $sectorId)
            ->pluck('name', 'id')->toArray();

        $this->selectedRoom = "";
    }
}
