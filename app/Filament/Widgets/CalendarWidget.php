<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\ReservationDate;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Database\Query\Builder;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public ?string $selectedRoom = null;

    public function fetchEvents(array $info): array
    {
        $selectedRoom = $this->selectedRoom;

        return ReservationDate::query()
            ->join('reservations', 'reservations.id', 'reservation_dates.reservation_id')
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
