<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
              <p></p>
              @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
