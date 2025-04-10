<x-filament-panels::page>
    <form class="space-y-6" wire:submit.prevent="submitForm">
        {{ $this->form }}
        <div class="flex flex-wrap gap-2">
            <div>
                <x-filament::button type="submit" form="submitForm" icon="heroicon-m-printer"
                x-on:click="window.open('{{ route('accounting.general-ledger', ['openBalance' => $openBalance]) }}', '_blank')">
                    Cetak
                </x-filament::button>
            </div>
            <div>
                <x-filament::button type="submit" form="submitForm" color="warning" icon="heroicon-m-archive-box-arrow-down">
                    Download
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
