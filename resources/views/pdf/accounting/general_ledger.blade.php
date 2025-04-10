<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/tailwind/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.accounting.partials.header')
    <main>
        <div class="text-3xl font-bold text-center">General Ledger</div>
    </main>
    {{-- @include('pdf.partials.footer') --}}
</body>

</html>
