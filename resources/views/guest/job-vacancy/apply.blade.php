<x-guest-layout>
    <div class="bg-blue-900" id="heading">
        <div class="w-4/5 mx-auto" style="padding: 100px 0">
            <div class="text-5xl text-white font-bold">{{ $job->title }}</div>
        </div>
    </div>

    <div class="job-list-wrapper bg-gray-100 py-5">
        <div class="job-list w-4/5 mx-auto">
            <livewire:applyjob />
        </div>
    </div>
</x-guest-layout>
