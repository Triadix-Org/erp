<div style="margin: 20px 0">
    <form method="POST">
        @csrf
        @method('PUT')


        <x-filament::input.wrapper>
            <x-filament::input label="NIP" type="text" wire:model="nip" value="{{ $record->nip }}" />
        </x-filament::input.wrapper>
    </form>

</div>
{{-- @dd($record) --}}
