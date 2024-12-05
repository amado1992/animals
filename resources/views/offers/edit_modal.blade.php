
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit offer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::model($offer, ['method' => 'PATCH', 'route' => ['offers.update', $offer->id], 'id' => 'offerForm'] ) !!}
                            @include('offers._form_modal', ['id' => 'editOfferForm', 'submitButtonText' => 'Edit offer'])
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
      </div>
  </div>
