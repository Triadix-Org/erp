<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/tailwind/style.css') }}" type="text/css" />
</head>

<body>
    {{-- @include('pdf.accounting.partials.header') --}}
    <div style="display: table; width: 100%;">
        <div style="display: table-cell; width: 8%;">
            <img src="{{ public_path('storage/logo/Logo.jpg') }}" class="w-16">
        </div>
        <div style="display: table-cell; vertical-align: middle; width: 92%;" class="text-left">
            <h2 style="font-weight: bold;" class="uppercase text-2xl">Buku Besar</h2>
            <h2 style="font-weight: bold;" class="text-xl">{{ $company->company_name }}</h2>
        </div>
    </div>
    <main>
        <div>Periode : </div>
    </main>
    {{-- @include('pdf.partials.footer') --}}
</body>

</html>
