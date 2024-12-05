<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Here below you will find the species without sales prices in <a href="{{env("APP_URL")}}/offers/{{$offer->id}}">Offer {{$offer->full_number}}</a> :</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 150px;">Species</td>
                    <td style="width: 30px;">Sales Curr</td>
                    <td style="width: 60px;">M</td>
                    <td style="width: 60px;">F</td>
                    <td style="width: 60px;">U</td>
                    <td style="width: 60px;">P</td>
                    <td style="width: 60px;">URL</td>
                </tr>
                @foreach ($surpluses_info as $species => $oursurplus)
                    <tr style="vertical-align: top;">
                        <td><span style="font-size: 13px; font-weight: bold;">{{ $species }}</span></td>
                        <td>{{ $oursurplus->cost_currency }}</td>
                        <td>{{ number_format($oursurplus->salePriceM, 2, '.', '') }}</td>
                        <td>{{ number_format($oursurplus->salePriceF, 2, '.', '') }}</td>
                        <td>{{ number_format($oursurplus->salePriceU, 2, '.', '') }}</td>
                        <td>{{ number_format($oursurplus->salePriceP, 2, '.', '') }}</td>
                        <td><a href="{{env("APP_URL")}}/our-surplus/{{$oursurplus->id}}">{{env("APP_URL")}}/our-surplus/{{$oursurplus->id}}</a></td>
                    </tr>
                @endforeach
            </table>
            <br>
            <br>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
