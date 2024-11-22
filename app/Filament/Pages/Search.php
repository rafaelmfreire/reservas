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

    public function mount()
    {
        $basename = basename(request()->getRequestUri());
        $this->sectors = User::where('is_admin', false)->pluck('name', 'title', 'id')->toArray();

        // reset($this->sectors);
        $this->selectedSector = $basename;
        $sector = User::where('name', $basename)->first()->id;

        $this->rooms = Room::where('user_id', $sector)
            ->whereHas('user', function ($query) {
                $query->where('is_admin', false);
            })->pluck('name', 'id')->toArray();

        $this->selectedRoom = "";
    }
}
