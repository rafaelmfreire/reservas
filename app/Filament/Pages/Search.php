<?php

namespace App\Filament\Pages;

use App\Models\Room;
use Filament\Pages\SimplePage;

class Search extends SimplePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.search';

    public array $rooms;
    public ?string $selectedRoom = null;

    public function mount()
    {
        $this->rooms = Room::pluck('name', 'id')->toArray();
        $this->selectedRoom = "";
    }
}
