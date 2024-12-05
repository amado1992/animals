<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{$contact->full_name}},</p>
            <p>We are welcoming you as a new relation of our organization, we would be pleased to receive from you a list with required specimens as well the specimens that are surplus to your collection. Our team might be at your services.</p>
            <p>You now have access to our inventory of surplus animals with all necessary details.</p>
            <p><a href="www.zoo-services.com" target="_blank">To our complete inventory with all information!</a></p>
            <p>Many thanks,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
