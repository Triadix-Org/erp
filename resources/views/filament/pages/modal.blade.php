<div style="margin: 20px 0">
    <div class="flex gap-x-4">
        <x-filament::button type="button" class="w-full py-8" icon="heroicon-s-document-text" iconSize="lg" tag="a"
            href="{{ url('sales/proforma-invoice/pdf') . '/' . $code }}" target="_blank">
            Proforma Invoice
        </x-filament::button>

        <x-filament::button type="button" class="w-full py-8" icon="heroicon-s-document-currency-dollar" iconSize="lg"
            tag="a" href="{{ url('sales/commercial-invoice/pdf') . '/' . $code }}" target="_blank">
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
