<div style="margin: 20px 0">
    <div class="flex gap-x-4">
        <x-filament::button type="button" class="w-full py-8" icon="heroicon-s-document-text" iconSize="lg" tag="a"
            href="{{ url('sales/sales-order/pdf') . '/' . $code }}" target="_blank">
            Production Order
        </x-filament::button>

        <x-filament::button type="button" class="w-full py-8" icon="heroicon-s-document-currency-dollar" iconSize="lg">
            Commercial Invoice
        </x-filament::button>

        <x-filament::button type="button" class="w-full py-8" icon="heroicon-s-queue-list" iconSize="lg">
            Packing List
        </x-filament::button>

        <x-filament::button type="button" class="w-full py-8" icon="heroicon-s-tag" iconSize="lg">
            Shipping Marks
        </x-filament::button>

    </div>
</div>
