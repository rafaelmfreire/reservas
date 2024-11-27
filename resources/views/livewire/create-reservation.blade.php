    <div>
        <form wire:submit="create">
            {{ $this->form }}

            <div class="mt-6">
                <button type="submit" class="font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-blue-500 text-white hover:bg-blue-500 focus-visible:ring-blue-500/50 dark:bg-blue-500 dark:hover:bg-blue-400 dark:focus-visible:ring-blue-400/50">
                    Solicitar pré-reserva
                </button>
            </div>
        </form>

        <x-filament-actions::modals />
    </div>