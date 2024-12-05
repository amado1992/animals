<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$contact->full_name}},</p>
            <p>We have noticed your application but we are very sorry to inform you that your information about your institution does not match with the profile-standards that we maintain at the moment.</p>
            <p>Many thanks for your interest to visit our website.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
