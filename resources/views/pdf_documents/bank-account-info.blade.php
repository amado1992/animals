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
                @foreach ($bankAccounts as $bankAccount)
                    <span style="font-weight: bold;">Name: </span>{!! $bankAccount->name !!}<br>
                    <span style="font-weight: bold;">Currency: </span>{!! $bankAccount->currency !!}<br>
                    <span style="font-weight: bold;">Beneficiary: </span>
                    @switch($bankAccount->company_name)
                        @case('IZS-BV')
                            International Zoo Services/John Rens BV
                            @break
                        @case('IZS-Inc')
                            International Zoo Services/John Rens INC
                            @break
                        @default
                            International Zoo Services/John Rens
                    @endswitch<br>
                    <span style="font-weight: bold;">Beneficiary address: </span><br>
                    {!! $bankAccount->company_address !!}<br>
                    <span style="font-weight: bold;">Beneficiary bank: </span>{{$bankAccount->beneficiary_name}}<br>
                    <span style="font-weight: bold;">Beneficiary bank address: </span>{{$bankAccount->beneficiary_address}}<br>
                    <span style="font-weight: bold;">Beneficiary account number: </span>{{$bankAccount->beneficiary_account}}<br>
                    <span style="font-weight: bold;">IBAN Code: </span>{{$bankAccount->iban}}<br>
                    <span style="font-weight: bold;">Swift: </span>{{$bankAccount->beneficiary_swift}}<br><br>
                @endforeach
            </div>
        </div>
    </body>
</html>
