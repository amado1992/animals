<html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Zoo Services">

        <style>
            @page {
                margin: 100px;
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
                <table class="table" style="font-size: 11px;" cellspacing="0">
                    <tbody>
                        <tr style="vertical-align: top;">
                            <td>
                                <img src="{{asset('img/logo.jpg')}}" alt="" style="width: 100px;">
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <b>INTERNATIONAL ZOO SERVICES BV</b><br>
                                Louis Couperusplein2. 2514 HP Den Haag. The Netherlands<br>
                                VAT number: NL800799227B02<br>
                                <b>email: </b><a href='mailto:izs@zoo-services.com' style="font-weight: bold;">info@zoo-services.com</a><br>
                                <b>web: </b><a href='https://www.zoo-services.com' style="font-weight: bold;">www.zoo-services.com</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row" style="float: right;">
                {{ \Carbon\Carbon::now()->format('F j, Y') }}
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <p>To whom it may concern,</p>
                <p>Our bank account is on the name John Rens B.V.</p>
                <p>International Zoo Services is the commercial name of the company which is registered by the Chamber of Commerce under the name John Rens BV</p>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <table class="table" cellspacing="0">
                    <tbody>
                        <tr style="vertical-align: top;">
                            <td>
                                <p>
                                    J.M. Rens.<br>
                                    <strong>INTERNATIONAL ZOO SERVICES BV</strong><br>
                                    Louis Couperusplein2. 2514 HP Den Haag.<br>
                                    The Netherlands<br>
                                    VAT number: NL800799227B02
                                </p>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <img src="{{asset('img/cuno.jpg')}}" alt="" style="width: 150px;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <img src="{{asset('img/handtekening_John.jpg')}}" alt="" style="width: 200px;"><br>
                _________________________________
            </div>

            <div class="row">&nbsp;</div>

            <div class="row">
                <p>
                    <span style="font-weight: bold;"><u>Banking details:</u></span>
                </p>
                <p>
                    <span style="font-weight: bold;">Beneficiary: </span>INTERNATIONAL ZOO SERVICES / John Rens BV<br>
                    <span style="font-weight: bold;">Beneficiary address: </span>Louis Couperusplein2 2514 HP Den Haag<br>
                    <span style="font-weight: bold;">Beneficiary bank: </span>ABN-AMRO Bank<br>
                    <span style="font-weight: bold;">Beneficiary bank address: </span>The Hague, The Netherlands<br>
                    <span style="font-weight: bold;">Beneficiary account number: </span>558803725<br>
                    <span style="font-weight: bold;">IBAN Code: </span>NL95ABNA0558803725<br>
                    <span style="font-weight: bold;">Swift: </span>ABNANL2A
                </p>
            </div>
        </div>
    </body>
</html>
