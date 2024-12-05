<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>IZS - Information System inform that a member contact us.</p>
            <p style="font-weight: bold;">Member details:</p>
            <p>
                Name: {{$contact->full_name}}<br>
                Email: {{$contact->email}}<br>
                City: {{$contact->city}}<br>
                Country: {{ ($contact->country) ? $contact->country->name : '' }}
                Institution: {{$contact->organisation->name}}<br>
                Type of institution: {{$contact->organisation->organisation_type}}
            </p>
            <p>
                <strong>Department:<strong> {{$department}}
            </p>
            <p>
                <strong>Member question:<strong> {{$message}}
            </p>
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
