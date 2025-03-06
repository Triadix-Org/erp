<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.partials.header')
    <main>
        <div id="details" class="clearfix">
            <div id="client" style="margin-top: 40px;">
                <div class="to">CUSTOMER:</div>
                <h2 class="name">{{ $customer['code'] }}</h2>
                <div class="address">{{ $customer['name'] }}</div>
                <div class="address">{{ $customer['pic'] }}</div>
            </div>
            <div id="invoice">
                <h1>PROFORMA INVOICE</h1>
                <div class="date">Number: {{ $inv_no }}</div>
                <div class="date">Date: {{ \Carbon\Carbon::parse($date)->format('d-m-Y H:i') }}</div>
            </div>
        </div>


    </main>
    @include('pdf.partials.footer')
</body>

</html>
