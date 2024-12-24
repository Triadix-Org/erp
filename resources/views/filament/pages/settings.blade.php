<x-filament-panels::page>
    <form class="space-y-6" wire:submit.prevent="submitForm">
        {{ $this->form }}
        <div class="w-full p-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-700">
            <x-filament::button type="submit" form="submitForm">
                Save
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
