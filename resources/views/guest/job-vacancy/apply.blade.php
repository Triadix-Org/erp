<x-guest-layout>
    <div class="bg-blue-900" id="heading">
        <div class="w-4/5 mx-auto" style="padding: 100px 0">
            <div class="text-5xl text-white font-bold">{{ $job->title }}</div>
        </div>
    </div>

    <div class="job-list-wrapper bg-gray-100 py-5">
        <div class="job-list w-4/5 mx-auto">
            <div>
                <form class="w-[50%] mx-auto bg-white rounded-xl p-8" id="formApply">
                    @csrf
                    <input type="hidden" name="job_id" value="{{ $job->id }}">
                    <div class="mb-5">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full
                            Name</label>
                        <input type="text" id="name" name="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"required />
                    </div>
                    <div class="mb-5">
                        <label for="phone"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Whatsapp/Phone</label>
                        <input type="text" id="phone" name="phone"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>
                    <div class="mb-5">
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input type="email" id="email" name="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>
                    <div class="mb-5">
                        <label for="date_of_birth"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date of
                            Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>
                    <div class="mb-5">
                        <label for="education"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Education</label>
                        <select id="education" name="education" name="education"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected>Choose a education</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="D1-D3">D1-D3</option>
                            <option value="D4/S1">D4/S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="years_of_experience"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Years
                            of Experience</label>
                        <input type="number" id="years_of_experience" name="years_of_experience"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>
                    <div class="mb-5">
                        <label for="resume"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Resume</label>
                        <input
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="resume" name="resume" type="file">
                    </div>
                    <div class="mb-5">
                        <label for="application_letter"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Application
                            Letter</label>
                        <input
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="application_letter" name="application_letter" type="file">
                    </div>
                    <div class="mb-5">
                        <label for="certificate"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Certificate</label>
                        <input
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="certificate" name="certificate" type="file">
                    </div>
                    <button type="button" onclick="apply()"
                        class="mt-5 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-md w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Apply</button>
                </form>
            </div>
        </div>
    </div>

</x-guest-layout>
<script>
    function apply() {
        var form = document.getElementById('formApply')
        var formData = new FormData(form)

        $.ajax({
            url: `{{ url('job/apply') }}`,
            data: formData,
            method: 'POST',
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                // showLoading();
            },
            success: (data) => {
                Swal.fire({
                    title: "Berhasil!",
                    type: "success",
                    icon: "success",
                })
            },
            error: function(error) {
                // hideLoading();
                handleErrorAjax(error)
            },
            complete: function() {
                // hideLoading();
            },
        })
    }
</script>
