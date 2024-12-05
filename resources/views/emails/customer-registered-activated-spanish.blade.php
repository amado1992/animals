<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Estimado(a) {{$contact->full_name}},</p>
            <p>Le damos la bienvenida como una nueva relación de nuestra organización, nos complacería recibir de usted una lista con las especies que usted requiera, así como las especies disponibles en su colección. Nuestro equipo está a su servicio.</p>
            <p>Ahora usted tiene acceso a nuestro inventario de animales disponibles con todos los detalles necesarios.</p>
            <p><a href="www.zoo-services.com" target="_blank">Hacia nuestro inventario completo con toda la informacion!</a></p>
            <p>Muchas gracias,</p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
