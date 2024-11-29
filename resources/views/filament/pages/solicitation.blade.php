<x-filament-panels::page.simple>
    <div class="flex items-center justify-between">
        <a href="/consultar/{{$this->selectedSector}}" class="flex">
            <div class="flex items-center space-x-2 border border-gray-400 text-gray-400 hover:text-white hover:border-white px-4 py-2 rounded-md">
                <svg class="h-5 w-5" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"></path>
                </svg>
                <span>Voltar para consulta</span>
            </div>
        </a>
    </div>
    @livewire('create-reservation', ['sector' => $this->selectedSector])
</x-filament-panels::page.simple>