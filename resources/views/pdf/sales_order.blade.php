<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Production Order</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.partials.header')
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">SALES BY:</div>
                <h2 class="name">{{ $sales_by['name'] }}</h2>
                <div class="address">Date: {{ $sales_date }}</div>
                <div class="address">Due Date: {{ $due_date }}</div>
            </div>
            <div id="invoice">
                <h1>PRODUCTION ORDER</h1>
                <div class="date">Number: {{ $code }}</div>
                <div class="date">Status: {{ $status_str }}</div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0" class="detail-table" id="sales-order-table">
            <thead>
                <tr>
                    <th style="width: 5%" class="no">#</th>
                    <th style="width: 15%" class="unit">PICTURE</th>
                    <th style="width: 30%" class="desc">ITEM</th>
                    <th style="width: 30%" class="unit">DESC</th>
                    <th style="width: 10%" class="desc">QTY</th>
                    <th style="width: 10%" class="unit">UNIT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $details)
                    <tr>
                        <td class="no">{{ $loop->iteration }}</td>
                        <td class="unit"><img style="width: 50px"
                                src="{{ public_path('storage/') . $details['product']['thumbnail'] }}" alt="">
                        </td>
                        <td class="desc">
                            {{ $details['product']['name'] }}
                        </td>
                        <td class="unit">
                            {{ $details['product']['desc'] }}
                        </td>
                        <td class="desc">{{ $details['qty'] }}</td>
                        <td class="unit">
                            {{ $details['product']['unit'] }}
                        </td>
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
                        <th style="width: 50%">Sales By</th>
                        <th style="width: 50%">Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img class="user-sign" src="{{ public_path('storage/' . $sales_by['sign']) }}"
                                alt="ttd">
                        </td>
                        <td><img class="user-sign" src="{{ public_path('storage/' . $approved_by['sign']) }}"
                                alt="ttd">
                        </td>
                    </tr>
                    <tr>
                        <td>{{ $sales_by['name'] }}</td>
                        <td>{{ $approved_by['name'] }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Sales Marketing Staff</td>
                        <td>Production Manager</td>
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
