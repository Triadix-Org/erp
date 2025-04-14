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
                <h2 class="name">{{ $purchaser['name'] }}</h2>
                <div class="address">Date: {{ $po_date }}</div>
            </div>
            <div id="invoice">
                <h1>PURCHASE ORDER</h1>
                <div class="date">Number: {{ $code }}</div>
            </div>
        </div>

        <div style="margin-bottom: 20px">
            <div><b>Vendor Details:</b></div>
            <div>{{ $supplier['code'] . ' - ' . $supplier['name'] }}</div>
            <div>{{ $supplier['pic'] }}</div>
            <div>{{ $supplier['handphone'] }}</div>
            <div>{{ $supplier['email'] }}</div>
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

        <table class="note-table">
            <tr>
                <td style="width:50%">
                    <div id="notices">
                        <div><b>Payment</b>:</div>
                        <div>{{ $supplier['payment_first'] . ' ' . $supplier['val_payment_first'] }}</div>
                        <div>{{ $supplier['payment_second'] . ' ' . $supplier['val_payment_second'] }}</div>
                    </div>
                </td>
                <td style="width:50%">
                    <div id="notices">
                        <div><b>Payment Terms</b> {{ $payment_terms }}</div>
                        <div><b>Incoterms</b>: {{ $incoterms }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="sign">
            <table class="signature-table">
                <thead>
                    <tr>
                        <th>Request By</th>
                        <th>Approved By</th>
                        <th>Approved By</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img class="user-sign" src="{{ public_path('storage/' . $purchaser['sign']) }}"
                                alt="ttd">
                        </td>
                        @if ($operational_by)
                            <td><img class="user-sign" src="{{ public_path('storage/' . $operational['sign']) }}"
                                    alt="ttd"></td>
                        @else
                            <td></td>
                        @endif
                        @if ($finance_by)
                            <td><img class="user-sign" src="{{ public_path('storage/' . $finance['sign']) }}"
                                    alt="ttd"></td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>{{ $purchaser['name'] }}</td>
                        <td>{{ $operational_by ? $operational['name'] : '' }}</td>
                        <td>{{ $finance_by ? $finance['name'] : '' }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Purchasing</td>
                        <td>Operational Manager</td>
                        <td>Finance Manager</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </main>
    @include('pdf.partials.footer')
</body>

</html>
