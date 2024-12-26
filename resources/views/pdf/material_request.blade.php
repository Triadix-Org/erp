<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Example 2</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.partials.header')
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">REQUEST BY:</div>
                <h2 class="name">{{ $req_by['name'] }}</h2>
                <div class="address">Date: {{ $date }}</div>
                <div class="address">Due Date: {{ $due_date }}</div>
            </div>
            <div id="invoice">
                <h1>MATERIAL REQUEST</h1>
                <div class="date">Number: {{ $code }}</div>
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
            <div>NOTE:</div>
            <div class="notice">{{ $note }}</div>
        </div>

        <div class="sign">
            <table class="signature-table">
                <thead>
                    <tr>
                        <th>Request By</th>
                        <th>Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img class="user-sign" src="{{ public_path('storage/' . $req_by['sign']) }}"
                                alt="ttd">
                        </td>
                        @if ($approved_by)
                            <td><img class="user-sign" src="{{ public_path('storage/' . $approved_by['sign']) }}"
                                    alt="ttd"></td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>{{ $req_by['name'] }}</td>
                        <td>{{ $approved_by ? $approved_by['name'] : '' }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Production Staff</td>
                        <td>Production Manager</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="visible-print">
            {!! QrCode::size(120)->generate(env('APP_URL') . 'sales/order-production/pdf/' . $code) !!}
        </div>
        {{-- <img src="data:image/png;base64, {!! base64_encode(
            QrCode::format('png')->size(120)->generate(env('APP_URL') . 'sales/order-production/pdf/' . $code),
        ) !!}" alt="QR Code"> --}}

    </main>
    @include('pdf.partials.footer')
</body>

</html>
