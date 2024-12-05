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
            <p>Sin embargo, nos gustaria recibir mas informacion acerca de su institucion; donde está localizada, y que animales mantiene?.</p>
            <p>Tiene usted alguna relación con algún zoológico reconocido?</p>
            <p>A la espera de su amable y pronta respuesta,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
