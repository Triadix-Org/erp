<header class="clearfix">
    <div id="logo">
        <img src="{{ public_path('storage/logo/Logo.jpg') }}">
    </div>
    {{-- @dd($company) --}}
    <div id="company">
        {{-- @dd($company) --}}
        <h2 class="name">{{ $company->company_name }}</h2>
        <div>{{ $company->address }}</div>
        <div>{{ $company->phone_one . ' - ' . $company->phone_two }}</div>
        <div><a href="mailto:company@example.com">{{ $company->email . ' | ' . $company->website }}</a></div>
    </div>
    </div>
</header>
