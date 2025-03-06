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
                <h1>INVOICE</h1>
                <div class="date">Number: {{ $inv_no }}</div>
                <div class="date">Date: {{ \Carbon\Carbon::parse($date)->format('d-m-Y H:i') }}</div>
            </div>
        </div>

        <table border="0" cellspacing="0" cellpadding="0" class="detail-table" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th style="width: 5%" class="no">#</th>
                    <th style="width: 40%" class="desc">ITEM</th>
                    <th style="width: 20%" class="unit">PRICE</th>
                    <th style="width: 10%" class="qty">QTY</th>
                    <th style="width: 25%" class="unit">TOTAL PRICE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $details)
                    <tr>
                        <td class="no">{{ $loop->iteration }}</td>
                        <td class="desc">
                            {{ $details['product']['name'] }}
                        </td>
                        <td class="unit right-column">
                            Rp {{ number_format($details['product']['price'], 0, ',', '.') }}
                        </td>
                        <td class="qty">{{ $details['qty'] }}</td>
                        <td class="unit right-column">
                            Rp {{ number_format($details['price_total'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table border="0" cellspacing="0" cellpadding="0" class="total-table" style="font-size: 9pt;">
            <tbody>
                <tr>
                    <td width="50%">Tax</td>
                    <td class="unit right-column" width="50%">
                        Rp {{ number_format($total_tax, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">Shipment Price</td>
                    <td class="unit right-column" width="50%">
                        Rp {{ number_format($shipment_price, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td width="50%">Total Amount</td>
                    <td class="unit right-column" width="50%">
                        Rp {{ number_format($total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

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
    </main>
    @include('pdf.partials.footer')
</body>

</html>
