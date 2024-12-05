<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Zoo Services">

        <style>
            @page {
                margin: 50px;
            }

            .pdf-container {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        <div class="pdf-container">
            <div class="row">
                <div class="col-md-12">
                    <table class="table" cellspacing="0">
                        <tbody>
                            <tr>
                                <td style="vertical-align: top;">
                                    <img src="{{asset('img/logo.jpg')}}" alt="" style="width: 70px;">
                                </td>
                                <td style="vertical-align: top;">
                                    @switch($order->bank_account->company_name)
                                        @case('IZS-BV')
                                            <b>INTERNATIONAL ZOO SERVICES BV</b><br>
                                            @break
                                        @case('IZS-Inc')
                                            <b>INTERNATIONAL ZOO SERVICES INC</b><br>
                                            @break
                                        @default
                                            <b>INTERNATIONAL ZOO SERVICES</b><br>
                                    @endswitch
                                    {{Str::of($order->bank_account->company_address)->replace('<br>', ', ')}}<br>
                                    {{$order->bank_account->beneficiary_address}}<br>
                                    @if ($order->bank_account->company_name == 'IZS-BV')
                                        VAT: NL800799227B02
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <p>
                    <b>PACKING LIST</b><br>
                    <b>AWB No.:</b> {{ $awbNo }}<br>
                    {{ \Carbon\Carbon::now()->format('F j, Y') }}<br>
                </p>
            </div>

            <div class="row">
                <table style="table-layout: unset; margin-right: 5px;" border="1px;" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr style="font-weight: bold; text-align: center;">
                            <th style="width: 60px;">Number</th>
                            <th style="width: 300px;">Items per craft</th>
                            <th style="width: 60px;">&nbsp;</th>
                            <th style="width: 60px;">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        @for ($i = 1; $i <= $listRows; $i++)
                            <tr>
                                <td style="text-align: center;">Craft {{ $i }}</td>
                                <td>&nbsp;{{ $speciesText }}</td>
                                <td style="text-align: center;">{{ $kgValue }} kg</td>
                                <td style="text-align: center;">One box</td>
                            </tr>
                        @endfor
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align: center;">{{ $kgValue * $listRows }} kg</td>
                            <td style="text-align: center;">{{ $listRows }} boxes</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <img src="{{asset('img/cuno.jpg')}}" alt="" style="width: 80px;">
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <img src="{{asset('img/handtekening_John.jpg')}}" alt="" style="width: 100px;">
            </div>
        </div>
    </body>
</html>
