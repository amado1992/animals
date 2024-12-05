<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>These are the last Surplus2 inserted, see below:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                <tr style="font-weight: bold;">
                    <td style="width: 10px; text-align: center;">M</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">F</td>
                    <td style="width: 1px; text-align: center;">-</td>
                    <td style="width: 10px; text-align: center;">U</td>
                    <td style="width: 150px;">Species</td>
                    <td style="width: 150px;">Supplier</td>
                </tr>
                @foreach ($new_surpluses as $surplus)
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
                            {{ $surplus->animal->scientific_name }}<br>
                            ({{ $surplus->animal->common_name }})
                        </td>
                        <td>
                            <div>
                                @if ($surplus->organisation != null)
                                    <b>Institution:</b> {{ $surplus->organisation->name }}<br>
                                    <b>Type:</b> {{ ($surplus->organisation->type) ? $surplus->organisation->type->label : '' }}<br>
                                    <b>Email:</b> {{ $surplus->organisation->email }}<br>
                                    <b>Country:</b> {{ ($surplus->organisation->country) ? $surplus->organisation->country->name : '' }}
                                @elseif ($surplus->contact != null)
                                    <b>Institution:</b> No institution.
                                    <b>Contact:</b> {{ $surplus->contact->fullname }}<br>
                                    <b>Email:</b> {{ $surplus->contact->email }}<br>
                                    <b>Country:</b> {{ ($surplus->contact->country) ? $surplus->contact->country->name : '' }}
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
