/* Update order (offer) cost status */
$(document).on('change', '.costform', function () {
   var id = $(this).attr('id');
   $.ajax({
      type: 'POST',
      url: $(this).find('input[name="route"]').val(),
      data: $('#' + id).serialize(),
      success: function (data) {
         if (data.error) {
            Swal.fire({
               title: 'Cost status',
               html: 'Cost status cannot be updated. Please notify the system administrator this info:<br /><br />Table: ' + data.table + '<br />Offer: ' + window.location.href,
               icon: 'warning',
               showCancelButton: true,
               cancelButtonText: 'Ok',
               confirmButtonClass: 'btn btn-success ms-2 mt-2 mr-2 accept',
               cancelButtonClass: 'btn btn-danger ms-2 mt-2',
               buttonsStyling: false,
               closeOnConfirm: true,
               showConfirmButton: false,
               closeOnCancel: true,
            });
         } else {
            var seloption = $('#' + id).find(':selected').val();
            $('#' + id + '.costform select').removeClass();
            $('#' + id + '.costform select').addClass(seloption);
         }
      }
   });
});

/* Update order cost background colors */
$('.costform').each(function () {
   var seloption = $('#' + $(this).attr('id') + '.costform').find(':selected').val();
   $('#' + $(this).attr('id') + '.costform select').removeClass();
   $('#' + $(this).attr('id') + '.costform select').addClass(seloption);
});
