<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$contact->full_name}},</p>
            <p>Thanks for registering, your application is now in the process of approval.</p>
            <p>After approval your account will be activated. Please have some patience, you will receive a message as soon as the application has been approved.</p>
            <p>Many thanks for visiting our website.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
