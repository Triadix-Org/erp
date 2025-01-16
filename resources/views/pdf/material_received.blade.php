<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Material Received Note - {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.partials.header')
    <main>
        <div id="details" class="clearfix">
            <div id="client" style="margin-top: 80px;">
                <div class="to">SUPPLIER:</div>
                <h2 class="name">{{ $supplier['code'] }}</h2>
                <div class="address">{{ $supplier['name'] }}</div>
                <div class="address">{{ $supplier['pic'] }}</div>
            </div>
            <div id="invoice">
                <h1>MATERIAL RECEIVED NOTE (TTB)</h1>
                <div class="date">Number: {{ $code }}</div>
                <div class="date">Date: {{ $date }}</div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" class="detail-table">
            <thead>
                <tr>
                    <th style="width: 5%" class="no">#</th>
                    <th style="width: 70%" class="desc">ITEM</th>
                    <th style="width: 15%" class="unit">UNIT</th>
                    <th style="width: 10%" class="qty">QTY</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $details)
                    <tr>
                        <td class="no">{{ $loop->iteration }}</td>
                        <td class="desc">
                            {{ $details['product']['name'] }}
                        </td>
                        <td class="unit">
                            {{ $details['product']['unit'] }}
                        </td>
                        <td class="qty">{{ $details['qty'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="notices">
            <div>COMMENT:</div>
            <div class="notice">{{ $comment }}</div>
        </div>
        <div id="notices">
            <div>RECEIVED CONDITION:</div>
            <div class="notice">{{ $received_condition }}</div>
        </div>

        <div class="sign">
            <table class="signature-table">
                <thead>
                    <tr>
                        <th width="60%"></th>
                        <th>Received By</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td><img class="user-sign" src="{{ public_path('storage/' . $rec_by['sign']) }}"
                                alt="ttd">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ $rec_by['name'] }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td>Warehouse Staff</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </main>
    @include('pdf.partials.footer')
</body>

</html>
