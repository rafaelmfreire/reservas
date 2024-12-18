<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\ReservationDate;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public ?string $selectedRoom = null;
    public ?string $selectedSector = null;

    public function fetchEvents(array $info): array
    {
        $selectedRoom = $this->selectedRoom;

        return ReservationDate::query()
            ->join('reservations', 'reservations.id', 'reservation_dates.reservation_id')
            ->join('rooms', 'rooms.id', 'reservations.room_id')
            ->when(Auth::check() && !Auth::user()->is_admin, function (Builder $query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->when(!Auth::check() && empty($selectedRoom), function (Builder $query) {
                $selectedSector = User::where('name', $this->selectedSector)->first()->id;
                $query->where('user_id', $selectedSector);
            })
            ->where('start_at', '>=', $info['start'])
            ->where('end_at', '<=', $info['end'])
            ->where('reservations.is_confirmed', true)
            ->when($selectedRoom, function (Builder $query, string $selectedRoom) {
                $query->where('room_id', $selectedRoom);
            })
            ->get()
            ->map(
                fn(ReservationDate $date) => [
                    'title' => $date->reservation->description,
                    'color' => $date->reservation->room->color,
                    'start' => $date->start_at,
                    'end' => $date->end_at,
                    'url' => ReservationResource::getUrl(name: 'edit', parameters: ['record' => $date->reservation]),
                    'shouldOpenUrlInNewTab' => false
                ]
            )
            ->all();
    }
}
