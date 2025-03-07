<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }}</title>
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
                <h1>QUOTATION</h1>
                <div class="date">Number: {{ $code }}</div>
                <div class="date">Date: {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</div>
            </div>
        </div>

        <table border="0" cellspacing="0" cellpadding="0" class="detail-table" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th style="width: 5%" class="no">#</th>
                    <th style="width: 40%" class="desc">ITEM</th>
                    <th style="width: 20%" class="unit">PRICE</th>
                    <th style="width: 10%" class="qty">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td class="no">{{ $loop->iteration }}</td>
                        <td class="desc">
                            {{ $detail['product']['name'] }}
                        </td>
                        <td class="unit right-column">
                            Rp {{ number_format($detail['product']['price'], 0, ',', '.') }}
                        </td>
                        <td class="qty">{{ $detail['qty'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
    @include('pdf.partials.footer')
</body>

</html>
