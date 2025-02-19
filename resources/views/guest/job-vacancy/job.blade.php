<x-guest-layout>
    <div class="bg-blue-900" id="heading">
        <div class="w-4/5 mx-auto" style="padding: 100px 0">
            <div class="text-5xl text-white font-bold">{{ $job->title }}</div>
        </div>
    </div>

    <div class="job-list-wrapper bg-gray-100 py-5">
        <div class="job-list w-4/5 mx-auto">
            <a href="{{ url('were-hiring') }}" class="text-gray-500"><i class="fa-solid fa-arrow-left-long"></i> Back to
                Home</a>
            <div class="flex gap-4 w-full">
                <div class="bg-white p-8 rounded-2xl" style="width: 60%">
                    <div class="job-desc">
                        <div class="text-xl font-bold mb-4">JOB DESCRIPTION</i>
                        </div>
                        <div class="text-lg">{!! nl2br(e($job->job_desc)) !!}</div>
                    </div>
                    <div class="job-requirements mt-5">
                        <div class="text-xl font-bold mb-4">REQUIREMENTS</i>
                        </div>
                        <div class="text-lg">{!! nl2br(e($job->job_requirements)) !!}</div>
                    </div>
                </div>
                <div style="width: 40%">
                    <div class="bg-white p-8 rounded-2xl">
                        <ul class="space-y-4 text-left text-lg text-gray-500">
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-users-viewfinder"></i>
                                <span>Department:</span>
                                <span class="text-gray-600 font-bold">{{ $job->dept_div }}</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-briefcase"></i>
                                <span>Working Type:</span>
                                <span
                                    class="text-gray-600 font-bold">{{ $job->contract_type_str . ' - ' . $job->working_type_str }}</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-graduation-cap"></i>
                                <span>Education:</span>
                                <span class="text-gray-600 font-bold">{{ $job->minimum_education }}</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-user-graduate"></i>
                                <span>Experience:</span>
                                <span class="text-gray-600 font-bold">{{ $job->years_of_experience }}</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-location-dot"></i>
                                <span>Location:</span>
                                <span class="text-gray-600 font-bold">{{ $job->location }}</span>
                            </li>
                            <li class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-clock"></i>
                                <span>Close:</span>
                                <span
                                    class="text-gray-600 font-bold">{{ \Carbon\Carbon::parse($job->close_date)->format('d-m-Y') }}</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ url('job') . '/' . $job->slug . '/apply' }}"
                        class="text-white w-full bg-blue-700 hover:bg-blue-800 focus:outline-none mt-4 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-lg px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Apply
                        <i class="fa-solid fa-paper-plane"></i></a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
