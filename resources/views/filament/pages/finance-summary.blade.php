<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <div class="grid grid-cols-3 gap-8">
        <div class="bg-blue-100 dark:bg-gray-800 p-6 rounded-xl">
            <div class="text-lg mb-3">Total income this month</div>
            <div class="flex justify-between items-center">
                <div class="text-left">
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $totalIncome }}</div>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="size-10 text-blue-500 dark:text-blue-300 fill-current">>
                        <path
                            d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-blue-100 dark:bg-gray-800 p-6 rounded-xl">
            <div class="text-lg mb-3">Total outcome this month</div>
            <div class="flex justify-between items-center">
                <div class="text-left">
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $totalOutcome }}</div>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="size-10 text-blue-500 dark:text-blue-300 fill-current">>
                        <path
                            d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
                    </svg>

                </div>
            </div>
        </div>
        <div class="bg-blue-100 dark:bg-gray-800 p-6 rounded-xl">
            <div class="text-lg mb-3">Profit this month</div>
            <div class="flex justify-between items-center">
                <div class="text-left">
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $totalProfit }}</div>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="size-10 text-blue-500 dark:text-blue-300 fill-current">>
                        <path
                            d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75ZM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 0 1-1.875-1.875V8.625ZM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 0 1 3 19.875v-6.75Z" />
                    </svg>

                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-8 mt-5">
        <div>
            @livewire(\App\Livewire\FinanceSummary\IncomeChart::class)
        </div>
        <div>
            @livewire(\App\Livewire\FinanceSummary\OutcomeChart::class)
        </div>
    </div>
</x-filament-panels::page>
