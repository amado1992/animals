<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>These are the last wanted records inserted, see below:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 150px;">Species</td>
                    <td style="width: 60px;">Looking for</td>
                    <td style="width: 150px;">Client</td>
                </tr>
                @foreach ($new_wanteds as $wanted)
                    <tr style="vertical-align: top;">
                        <td style="white-space: nowrap;">
                            {{ $wanted->animal->scientific_name }}<br>
                            ({{ $wanted->animal->common_name }})
                        </td>
                        <td>
                            {{ $wanted->looking_field }}
                        </td>
                        <td>
                            <div>
                                @if ($wanted->organisation != null)
                                    <b>Institution:</b> {{ $wanted->organisation->name }}<br>
                                    <b>Type:</b> {{ ($wanted->organisation->type) ? $wanted->organisation->type->label : '' }}<br>
                                    <b>Email:</b> {{ $wanted->organisation->email }}<br>
                                    <b>Country:</b> {{ ($wanted->organisation->country) ? $wanted->organisation->country->name : '' }}
                                @elseif ($wanted->client != null)
                                    <b>Institution:</b> No institution.
                                    <b>Contact:</b> {{ $wanted->client->fullname }}<br>
                                    <b>Email:</b> {{ $wanted->client->email }}<br>
                                    <b>Country:</b> {{ ($wanted->client->country) ? $wanted->client->country->name : '' }}
                                @endif
                            </div>
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
