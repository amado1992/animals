<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>IZS - Information System inform that a user contact us.</p>
            <p style="font-weight: bold;">User details:</p>
            <p>
                Name: {{$name}}<br>
                Email: {{$email}}<br>
                Institution: {{$institution}}<br>
                Country: {{$country}}
            </p>
            <p>
                <strong>Member question:<strong> {{$mess}}
            </p>
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
