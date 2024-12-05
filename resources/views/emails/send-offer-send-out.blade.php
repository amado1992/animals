<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <strong>New offer recently sent</strong>
            {!! $body  !!}
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
