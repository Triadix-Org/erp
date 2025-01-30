<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <div class="grid grid-cols-3 gap-8">
        <div class="bg-blue-100 dark:bg-blue-600 p-6 rounded-xl">
            <div class="flex justify-between items-center">
                <div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="size-10 text-blue-500 dark:text-blue-300 fill-current">
                        <path fill-rule="evenodd"
                            d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 0 0 4.25 22.5h15.5a1.875 1.875 0 0 0 1.865-2.071l-1.263-12a1.875 1.875 0 0 0-1.865-1.679H16.5V6a4.5 4.5 0 1 0-9 0ZM12 3a3 3 0 0 0-3 3v.75h6V6a3 3 0 0 0-3-3Zm-3 8.25a3 3 0 1 0 6 0v-.75a.75.75 0 0 1 1.5 0v.75a4.5 4.5 0 1 1-9 0v-.75a.75.75 0 0 1 1.5 0v.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ $totalOrders }}</div>
                    <div>Orders</div>
                </div>
            </div>
        </div>
        <div class="bg-blue-100 dark:bg-blue-600 p-6 rounded-xl">
            <div class="flex justify-between items-center">
                <div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="size-10 text-blue-500 dark:text-blue-300 fill-current">
                        <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                        <path fill-rule="evenodd"
                            d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"
                            clip-rule="evenodd" />

                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">Rp. {{ $totalAmount }}</div>
                    <div>Total Amount</div>
                </div>
            </div>
        </div>
        <div class="bg-blue-100 dark:bg-blue-600 p-6 rounded-xl">
            <div class="flex justify-between items-center">
                <div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        class="size-10 text-blue-500 dark:text-blue-300 fill-current">
                        <path fill-rule="evenodd"
                            d="M2.25 2.25a.75.75 0 0 0 0 1.5H3v10.5a3 3 0 0 0 3 3h1.21l-1.172 3.513a.75.75 0 0 0 1.424.474l.329-.987h8.418l.33.987a.75.75 0 0 0 1.422-.474l-1.17-3.513H18a3 3 0 0 0 3-3V3.75h.75a.75.75 0 0 0 0-1.5H2.25Zm6.54 15h6.42l.5 1.5H8.29l.5-1.5Zm8.085-8.995a.75.75 0 1 0-.75-1.299 12.81 12.81 0 0 0-3.558 3.05L11.03 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l2.47-2.47 1.617 1.618a.75.75 0 0 0 1.146-.102 11.312 11.312 0 0 1 3.612-3.321Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">IDR 200.000.000,00</div>
                    <div>Profit</div>
                </div>
            </div>
        </div>
    </div>
    {{ $this->table }}
</x-filament-panels::page>
