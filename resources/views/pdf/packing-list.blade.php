<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Packing List</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.partials.header')
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">BUYER:</div>
                <h2 class="name">{{ $customer['name'] }}</h2>
                <table border="0">
                    <tbody>
                        <tr>
                            <td>Code</td>
                            <td>: {{$customer['code']}}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>: {{$customer['address']}}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>: {{$customer['phone']}}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: {{$customer['email']}}</td>
                        </tr>
                    </tbody>
                </table>
                {{-- <div class="address">Code: {{ $customer['code'] }}</div>
                <div class="address">Address: {{ $customer['address'] }}</div>
                <div class="address">Phone: {{ $customer['phone'] }}</div>
                <div class="address">Mail: {{ $customer['email'] }}</div> --}}
            </div>
            <div id="invoice">
                <h1>PACKING LIST</h1>
                <div class="date">Invoice Number: {{ $inv_no }}</div>
                <div class="date">Date: {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" class="detail-table" id="sales-order-table">
            <thead>
                <tr>
                    <th style="width: 5%" class="no">#</th>
                    <th style="width: 35%" class="desc">ITEM</th>
                    <th style="width: 10%" class="unit">QTY</th>
                    <th style="width: 10%" class="desc">UNIT</th>
                    <th style="width: 20%" class="unit">DIMENSION (CM)</th>
                    <th style="width: 20%" class="desc">WEIGHT (KG)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $details)
                    <tr>
                        <td class="no">{{ $loop->iteration }}</td>
                        <td class="desc">{{ $details['product']['name'] }}
                        </td>
                        <td class="unit">
                            {{ $details['qty'] }}
                        </td>
                        <td class="desc">
                            {{ $details['product']['unit'] }}
                        </td>
                        <td class="unit">{{ $details['product']['dimension'] }}</td>
                        <td class="desc" style="text-align: right">
                            {{ $details['product']['weight'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table border="0" cellspacing="0" cellpadding="0" class="total-table" style="font-size: 9pt;">
            <tbody>
                <tr>
                    <td width="60%">Total Weight</td>
                    <td class="unit right-column" width="40%">
                        {{ $total_weight }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- <div id="notices">
            <div>NOTE:</div>
            <div class="notice">{{ $note }}</div>
        </div> --}}

        <div class="sign">
            <table class="signature-table">
                <thead>
                    <tr>
                        <th width="60%"></th>
                        <th>Best Regards,</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td><img class="user-sign" src="{{ public_path('storage/' . $user['sign']) }}" alt="ttd">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>{{ $user['name'] }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td>Sales & Marketing</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {{-- <img src="data:image/png;base64, {!! base64_encode(
            QrCode::format('png')->size(120)->generate(env('APP_URL') . 'sales/order-production/pdf/' . $code),
        ) !!}" alt="QR Code"> --}}

    </main>
    @include('pdf.partials.footer')
</body>

</html>
