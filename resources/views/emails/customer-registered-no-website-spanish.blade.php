<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Estimado(a) {{$contact->full_name}},</p>
            <p>Le agradecemos su registro en nuestro sitio web.</p>
            <p>Debido a que parece que usted no posee sitio web o página de Facebook, le solicitamos que nos informe más acerca de su granja de cría; ¿Qué especies mantiene, resultados de reproducción, contactos con zoológicos, etc.?</p>
            <p>A la espera de su amable y pronta respuesta,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
