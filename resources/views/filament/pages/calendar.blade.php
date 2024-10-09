<x-filament-panels::page>
    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.live="selectedRoom">
            <option value="">Todas</option>
            @foreach($rooms as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>

    @livewire(\App\Filament\Widgets\CalendarWidget::class,
    ['selectedRoom' => $selectedRoom],
    key(str()->random()))
</x-filament-panels::page>