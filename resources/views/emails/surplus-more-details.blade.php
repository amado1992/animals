<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear contact_name,</p>
            <p>Many thanks for sending the details of your surplus survey; however we do not received all necessary details.</p>
            <p>Can you please help us sending more information regarding following subjects?</p>
            <p>
                Please inform us about all following subjects:<br>
                Quantities – Sexes – Ages - Captive bred/Wild-caught - Price proposition.
            </p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 10px; text-align: center;">M</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">F</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">U</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr style="vertical-align: top;">
                    <td style="text-align: center;">
                        {{ $surplus->male_quantity }}
                    </td>
                    <td></td>
                    <td style="text-align: center;">
                        {{ $surplus->female_quantity }}
                    </td>
                    <td></td>
                    <td style="text-align: center;">
                        {{ $surplus->unknown_quantity }}
                    </td>
                    <td style="white-space: nowrap;">
                        {{ $surplus->animal->scientific_name}} ({{ $surplus->animal->common_name}})
                    </td>
                </tr>
            </table><br>
            <p>Let us know soon and we thank you for your cooperation.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
