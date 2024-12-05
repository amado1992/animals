<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$order->supplier->letter_name}},</p>
            <p>
                Find enclosed Checklist with details for document application for the reserved species:
                <table style="font-size: 13px;" border="1" cellpadding="0" cellspacing="0">
                    <tr style="font-weight: bold; text-align: center;">
                        <td style="width: 10px;">M</td>
                        <td style="width: 1px;">-</td>
                        <td style="width: 10px;">F</td>
                        <td style="width: 1px;">-</td>
                        <td style="width: 10px;">U</td>
                        <td style="width: 220px;">Species</td>
                    </tr>
                    @foreach ($order->offer->species_ordered as $species)
                        <tr style="vertical-align: top;">
                            <td style="text-align: center;">
                                {{ ($species->offerQuantityM < 0) ? 'x' : $species->offerQuantityM }}
                            </td>
                            <td></td>
                            <td style="text-align: center;">
                                {{ ($species->offerQuantityF < 0) ? 'x' : $species->offerQuantityF }}
                            </td>
                            <td></td>
                            <td style="text-align: center;">
                                {{ ($species->offerQuantityU < 0) ? 'x' : $species->offerQuantityU }}
                            </td>
                            <td style="white-space: nowrap;">
                                {{ $species->oursurplus->animal->scientific_name}} ({{ $species->oursurplus->animal->common_name}})
                            </td>
                        </tr>
                    @endforeach
                </table><br>
            </p>
            <p>Please let us know when permits/documents are applied, and how long approximately you estimate the process would take.</p>
            <p>Your kind cooperation is much appreciated. Thanks.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
