<x-filament-panels::page.simple class="sm:!max-w-full">

    <div class="flex items-center justify-between">
        <a href="/" class="flex">
            <div class="flex items-center space-x-2 border border-gray-400 text-gray-400 hover:text-white hover:border-white px-4 py-2 rounded-md">
                <svg class="h-5 w-5" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"></path>
                </svg>
                <span>Consultar de outro setor</span>
            </div>
        </a>
        <a href="/solicitar/{{$this->selectedSector}}" class="flex">
            <div class="flex items-center space-x-2 border border-blue-700 hover:bg-blue-600 hover:border-blue-600 bg-blue-700 text-white px-4 py-2 rounded-md">
                <svg class="w-5 h-5" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                <span>Solicitar Reserva</span>
            </div>
        </a>
    </div>

    <div>
        <p class="mb-2">Ver reservas de:</p>
        <x-filament::input.wrapper>
            <x-filament::input.select wire:model.change="selectedRoom">
                <option value="">Todas as salas</option>
                @foreach($rooms as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    @livewire(\App\Filament\Widgets\CalendarWidget::class,
    ['selectedRoom' => $selectedRoom, 'selectedSector' => $selectedSector],
    key(str()->random()))
</x-filament-panels::page.simple>