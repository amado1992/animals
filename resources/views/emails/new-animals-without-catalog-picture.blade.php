<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>These species in surplus or wanted are without catalog picture, see below:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 70px;">Scientific name</td>
                    <td style="width: 70px;">Common name</td>
                </tr>
                @foreach ($species_without_catalog_picture as $species)
                    <tr style="vertical-align: top;">
                        <td>
                            {{ $species->scientific_name }}
                        </td>
                        <td>
                            {{ $species->common_name }}
                        </td>
                    </tr>
                @endforeach
            </table>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
