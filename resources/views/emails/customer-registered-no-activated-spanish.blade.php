<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Estimado(a) {{$contact->full_name}},</p>
            <p>Hemos notado su registro en nuestro sitio web, pero lamentamos mucho informarle que la información acerca de su institución no cumple con el perfil que mantenemos en estos momentos.</p>
            <p>Muchas gracias por su interés en visitar nuestro sitio web.</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
