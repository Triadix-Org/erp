<?php
    $hour = \Carbon\Carbon::now()->hour;
    $good = "";
    if ($hour >= 5 && $hour < 12) {
        $good = "Selamat Pagi,";
    } elseif ($hour >= 12 && $hour < 15) {
        $good = "Selamat Siang,";
    } elseif ($hour >= 15 && $hour < 18) {
        $good = "Selamat Sore,";
    } else {
        $good = "Selamat Malam,";
    }

    $user = auth()->user();
?>
<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <div class="bg-white w-full rounded-xl border px-10 py-2">
        <div class="flex gap-10 items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-teal-500 mb-4">
                    {{$good}} {{$user->name}}
                </div>
                <div class="text-xl">
                    Selamat datang di XERP!
                </div>
            </div>
            <div>
                <img class="w-[150px]" src="{{asset('images/illustration.png')}}" alt="">
            </div>
        </div>
    </div>
</x-filament-panels::page>