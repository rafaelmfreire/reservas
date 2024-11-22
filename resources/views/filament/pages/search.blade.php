<x-filament-panels::page.simple class="sm:!max-w-full">

    <ul class="flex items-center space-x-4">
        @foreach($sectors as $sector)
        <a href="/consultar/{{$sector}}">
            <li>{{ $sector }}</li>
        </a>
        @endforeach
    </ul>

    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="selectedRoom">
            <option value="">Todas as salas</option>
            @foreach($rooms as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>

    @livewire(\App\Filament\Widgets\CalendarWidget::class,
    ['selectedRoom' => $selectedRoom, 'selectedSector' => basename(request()->getRequestUri())],
    key(str()->random()))
</x-filament-panels::page.simple>