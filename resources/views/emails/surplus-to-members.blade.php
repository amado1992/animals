<!doctype html>
<html>
  @include('emails.email-header')
  <body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td class="container">
          <div class="content">
            <p>Dear {{ (isset($contact)) ? $contact->letter_name : 'zoo-relation' }},</p>
            <p>
                As registered visitor of our website you have the advantage to get regularly an update of the last few specimens that are added to our inventory.<br>
                We daily got new animals offered from zoos worldwide for which we are looking a new destination.
            </p>
            <p>Here below are the <u>new available species</u> that are offered to us recently:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                @foreach ($surplusToMembers as $surplus)
                    <tr style="vertical-align: top;">
                        <td><li></li></td>
                        <td>
                            <img alt="&#x1F4F7;" src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic) }}" width="95" height="95" style="border:0;" />
                        </td>
                        <td style="font-weight: bold;">
                            {{ $surplus->animal->scientific_name }} ({{ $surplus->animal->common_name }})
                        </td>
                    </tr>
                @endforeach
            </table>
            <p>/**********************************************************************************/</p>
            <p>Estimado(a) {{ (isset($contact)) ? $contact->letter_name : 'relación de zoológico' }},</p>
            <p>
                Como visitante registrado de nuestro sitio web, tiene la ventaja de recibir periódicamente una actualización de los últimos ejemplares que se agregan a nuestro inventario.<br>
                Diariamente recibimos nuevos animales de zoológicos de todo el mundo para los cuales estamos buscando un nuevo destino.
            </p>
            <p>A continuación se detallan las <u>nuevas especies disponibles</u> que se nos ofrecieron recientemente:</p>
            <table style="font-size: 13px;" border="0" cellpadding="0" cellspacing="0">
                @foreach ($surplusToMembers as $surplus)
                    <tr style="vertical-align: top;">
                        <td><li></li></td>
                        <td>
                            <img alt="&#x1F4F7;" src="{{ asset('storage/animals_pictures/'.$surplus->animal->id.'/'.$surplus->animal->catalog_pic) }}" width="95" height="95" style="border:0;" />
                        </td>
                        <td style="font-weight: bold;">
                            {{ $surplus->animal->scientific_name }} ({{ $surplus->animal->common_name }})
                        </td>
                    </tr>
                @endforeach
            </table>
            <br>
            <p>Unsubscribe, click/Unsubscribrise, click: <a href='mailto:unsubscribe@zoo-services.com?subject=UNSUBSCRIBE' target='_blank' style='color:#6216fe; text-decoration:underline;'>here/aqui</a></p>
            @include('emails.email-signature')
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>
