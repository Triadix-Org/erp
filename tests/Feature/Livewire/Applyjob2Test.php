<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('applyjob2');

    $component->assertSee('');
});
