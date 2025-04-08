<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <div class="grid grid-cols-2 gap-4">
        <div>
            {{ $this->form }}
        </div>
        <div>
            {{-- <div class="p-4 bg-blue-500 rounded-xl">{{ $this->status }}</div> --}}
        </div>
    </div>
    {{ $this->table }}
</x-filament-panels::page>
