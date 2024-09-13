<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use App\Models\ReservationDate;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $info): array
    {
        return ReservationDate::query()
            ->join('reservations', 'reservations.id', 'reservation_dates.reservation_id')
            ->where('start_at', '>=', $info['start'])
            ->where('end_at', '<=', $info['end'])
            ->where('reservations.is_confirmed', true)
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
