<?php

namespace App\Filament\Pages;

use App\Models\Room;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.calendar';

    public array $rooms;
    public ?string $selectedRoom = null;

    public function mount()
    {
        $this->rooms = Room::pluck('name', 'id')->toArray();
        $this->selectedRoom = "";
    }

    public function getTitle(): string | Htmlable
    {
        return __('Calendar');
    }

    public static function getNavigationLabel(): string
    {
        return __('Calendar');
    }
}
