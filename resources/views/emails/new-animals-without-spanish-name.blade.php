<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Here below you will find the animals without spanish name that are inside the standard list:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 70px;">Scientific name</td>
                    <td style="width: 70px;">Common name</td>
                    <td style="width: 70px;">Spanish name</td>
                </tr>
                @foreach ($animals as $animal)
                    <tr style="vertical-align: top;">
                        <td>
                            {{ $animal->scientific_name }}
                        </td>
                        <td>
                            {{ $animal->common_name }}
                        </td>
                        <td>
                            {{ $animal->spanish_name }}
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
