<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected offers" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'offers.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected offers</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('offer_status', 'Status', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_offer_status', Arr::except($offerStatuses, ['all']), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('offer_status_level', 'Status Level', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_offer_status_level', Arr::except($offerStatusesLevel, ['all']), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('offer_currency', 'Currency', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_offer_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('offer_type', 'Offer type', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('edit_offer_type', $price_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    {!! Form::label('client', 'Client', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="default" style="width: 100%" name="edit_selection_client_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    {!! Form::label('supplier', 'Supplier', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="default" style="width: 100%" name="edit_selection_supplier_id"></select>
                </div>
            </div>
            @if (Auth::user()->hasRole('admin'))
                <div class="row">
                    <div class="col-md-4">
                        {!! Form::label('manager', 'Manager', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('edit_offer_manager', $admins, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save offers', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
