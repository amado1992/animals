<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear contact_name,</p>
            <p>We can offer:</p>
            <p style="font-weight: bold;">
                {{ $surplus->male_quantity }}-{{ $surplus->female_quantity }}-{{ $surplus->unknown_quantity }} {{ $surplus->animal->common_name }} ({{ $surplus->animal->scientific_name }})
                @if ($surplus->age_field != '')
                    , {{ $surplus->age_field }}
                @endif
                @if (trim($surplus->bornYear) != '' && $surplus->bornYear != null)
                    , born: {{ $surplus->bornYear }}
                @endif
                @if (trim($surplus->remarks) != '' && $surplus->remarks != null)
                    , {{ $surplus->remarks }}
                @endif
                <br>
                @if ($surplus->location != '')
                    {{ $surplus->location }}
                @endif
            </p>
            <p>Exchange for your surplus specimens can also be considered; please note that the destination of your surplus animals is subject to your approval.</p>
            <p>In case you are interested: please contact us for more details: <a href='mailto:info@zoo-services.com'>info@zoo-services.com</a></p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
