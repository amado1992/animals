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
            <p>Sin embargo, hemos intentado visitar su sitio web, pero no podemos abrirlo.</p>
            <p>Por favor, haganos saber donde podemos encontrar mas informacion sobre su institucion?.</p>
            <p>A la espera de su amable y pronta respuesta,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
