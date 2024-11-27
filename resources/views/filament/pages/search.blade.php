<x-filament-panels::page.simple class="sm:!max-w-full">

    <a href="/solicitar" class="flex">
        <div class="flex items-center space-x-2 border border-blue-600 text-blue-600 px-4 py-2 rounded-md">
            <svg class="w-5 h-5" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
            </svg>
            <span>Solicitar Nova Reserva</span>
        </div>
    </a>
    <h2>Ver salas do:</h2>
    <ul class="flex items-center space-x-4">
        @foreach($sectors as $sec)
        <a href="/consultar/{{$sec}}" class="{{ strtolower($sec) == strtolower($this->selectedSector) ? 'text-white bg-blue-500 px-3 py-1 font-bold rounded-md' : 'text-blue-500' }}">
            <li>{{ $sec }}</li>
        </a>
        @endforeach
    </ul>

    <x-filament::input.wrapper>
        <x-filament::input.select wire:model.change="selectedRoom">
            <option value="">Todas as salas</option>
            @foreach($rooms as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>

    @livewire(\App\Filament\Widgets\CalendarWidget::class,
    ['selectedRoom' => $selectedRoom, 'selectedSector' => $selectedSector],
    key(str()->random()))
</x-filament-panels::page.simple>