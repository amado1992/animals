<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected offers" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'orders.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected orders</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex align-items-start">
                        <div class="mr-3">
                            {!! Form::checkbox('set_number_year', null) !!}
                            {!! Form::label('set_number_year', 'Set number and creation date: ') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="d-none" id="setNumberAndCreationDate">
                        <div class="d-flex form-group">
                            {!! Form::label('order_number', 'Order number') !!}
                            {!! Form::number('order_number', null, ['class' => 'form-control ml-2 mr-3']) !!}
                            {!! Form::label('created_date', 'Created date') !!}
                            {!! Form::date('created_at', (isset($order)) ? $order->created_date : null, ['class' => 'form-control ml-2']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status', 'Status *') !!}
                        {!! Form::select('order_status', $orderStatuses, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('manager', 'Manager *') !!}
                        {!! Form::select('manager_id', $admins, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('country', 'Destination country *') !!}
                        {!! Form::select('delivery_country_id', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('airport', 'Destination airport *') !!}
                        {!! Form::select('delivery_airport_id', [], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        {!! Form::hidden('hidden_delivery_airport_id', isset($order) ? $order->delivery_airport_id : null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cost_currency', 'Cost currency *') !!}
                        {!! Form::select('cost_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cost_price_type', 'Cost price type *') !!}
                        {!! Form::select('cost_price_type', $price_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sale_currency', 'Sale currency *') !!}
                        {!! Form::select('sale_currency', $currencies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('sale_price_type', 'Sale price type *') !!}
                        {!! Form::select('sale_price_type', $price_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cost_price_status', 'Cost price status *') !!}
                        {!! Form::select('cost_price_status', ['Estimation' => 'Estimation', 'Exactly' => 'Exactly'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('company', 'Company *') !!}
                        {!! Form::select('company', $companies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('bank_account', 'Bank account *') !!}
                        {!! Form::select('bank_account_id', $bankAccounts, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save offers', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
