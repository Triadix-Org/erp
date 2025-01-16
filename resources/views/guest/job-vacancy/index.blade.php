<x-guest-layout>
    <div class="bg-blue-900" id="heading">
        <div class="w-4/5 mx-auto" style="padding: 100px 0">
            <div class="text-5xl text-white font-bold">WE'RE HIRING!</div>
        </div>
    </div>

    <div id="jobVacancy" class="w-4/5 mx-auto" style="padding: 50px 0">
        <div class="text-3xl font-bold text-gray-800">Position Available</div>
        @foreach ($vacancy as $job)
            <div class="open-position mt-4 flex items-center justify-between  border-2 rounded-xl p-6">
                <div>
                    <div class="text-xl font-bold text-gray-600">{{ $job->title }}</div>
                    <div class="text-gray-500 mt-2"><span
                            class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">{{ $job->contract_type_str }}</span>
                        <span
                            class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">{{ $job->working_type_str }}</span>
                        <span
                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">{{ $job->minimum_education }}</span>
                    </div>
                </div>
                <a href="{{ url('job') . '/' . $job->slug }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Apply</a>
            </div>
        @endforeach
    </div>
</x-guest-layout>
