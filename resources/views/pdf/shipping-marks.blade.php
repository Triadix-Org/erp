<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Packing List</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf/style.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ public_path('css/pdf/tailwind/style.css') }}" type="text/css" />
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body>
    <main>
        @foreach ($detail as $item)
            <div class="mark-container">
                <div class="clearfix">
                    <div style="float: left;">
                        <div class="text-5xl font-sans font-bold text-black mt-5">SHIPPING MARKS</div>
                    </div>
                    <div style="float: right; text-align: center;">
                        <img width="75px" src="{{ public_path('storage/logo/Logo.jpg') }}">
                    </div>
                </div>
                <hr class="my-4" style="border: 0.5px solid black">

                <table class=" w-full text-2xl text-black font-bold font-sans">
                    <tbody>
                        <tr>
                            <td width="30%">INV NUMBER</td>
                            <td width="70%">: {{ $inv_no }}</td>
                        </tr>
                        <tr>
                            <td width="30%">ITEM</td>
                            <td width="70%">: {{ $item['product']['name'] }}</td>
                        </tr>
                        <tr>
                            <td width="30%">DIMENSION</td>
                            <td width="70%">: {{ $item['product']['dimension'] }} cm</td>
                        </tr>
                        <tr>
                            <td width="30%">WEIGHT</td>
                            <td width="70%">: {{ $item['product']['weight'] }} Kg</td>
                        </tr>
                    </tbody>
                </table>

                <hr class="my-4" style="border: 0.5px solid black">

                <table class=" w-full text-2xl text-black font-bold font-sans">
                    <tbody>
                        <tr>
                            <td width="30%">SHIP DATE</td>
                            <td width="70%">: {{ $ship_date }}</td>
                        </tr>
                        <tr>
                            <td width="30%">DESTINATION</td>
                            <td width="70%">: {{ $destination_country }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </main>
</body>

</html>
