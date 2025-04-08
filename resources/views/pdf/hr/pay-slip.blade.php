<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Example 2</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ public_path('css/pdf/tailwind/style.css') }}" type="text/css" />
</head>

<body>
    @include('pdf.partials.header')
    <main>
        <div class="clearfix">
            <div id="invoice">
                <h1>PAY SLIP / SALARY SLIP</h1>
                <div class="date">{{ $payroll['month'] }} {{ $payroll['year'] }}</div>
            </div>
        </div>

        <div class="employee-detail mt-4 border border-2 rounded-md p-4">
            <table class="w-full font-bold">
                <tbody>
                    <tr>
                        <td width="15%">Name</td>
                        <td width="35%">: {{ $employee['name'] }}</td>
                        <td width="15%">Position</td>
                        <td width="35%">: {{ $employee['personnel']['position'] }}</td>
                    </tr>
                    <tr>
                        <td width="15%">NIP</td>
                        <td width="35%">: {{ $employee['nip'] }}</td>
                        <td width="15%">Email</td>
                        <td width="35%">: {{ $employee['email'] }}</td>
                    </tr>
                    <tr>
                        <td width="15%">Department</td>
                        <td width="35%">: {{ $employee['personnel']['dept']['name'] }}</td>
                        <td width="15%">Division</td>
                        <td width="35%">: {{ $employee['personnel']['div']['name'] }}</td>
                    </tr>
                </tbody>
            </table> 
        </div>

        <div class="mt-6">
            <table border="0" cellspacing="0" cellpadding="0" class="detail-table">
                <thead>
                    <tr>
                        <th style="width: 25%" class="desc">INCOME</th>
                        <th style="width: 25%" class="unit"></th>
                        <th style="width: 25%" class="desc">REDUCTION</th>
                        <th style="width: 25%" class="unit"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="desc">Gaji Pokok</td>
                        <td class="unit">Rp. {{ number_format($salary, 0, ',', '.') }}</td>
                        <td class="desc">Potongan</td>
                        <td class="unit">Rp. {{ number_format($cut, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="desc">Bonus Lembur</td>
                        <td class="unit">Rp. {{ number_format($overtime, 0, ',', '.') }}</td>
                        <td class="desc"></td>
                        <td class="unit"></td>
                    </tr>
                    <tr>
                        <td class="desc">Tunjangan</td>
                        <td class="unit">Rp. {{ number_format($bonus, 0, ',', '.') }}</td>
                        <td class="desc"></td>
                        <td class="unit"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <table border="0" cellspacing="0" cellpadding="0" class="detail-table font-bold">
                <tbody>
                    <tr>
                        <td class="desc" width="25%">TOTAL THP</td>
                        <td class="unit" width="25%">Rp. {{ number_format($total, 0, ',', '.') }}</td>
                        <td width="25%"></td>
                        <td width="25%"></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>
    @include('pdf.partials.footer')
</body>

</html>
