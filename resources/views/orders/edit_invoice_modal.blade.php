<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit invoice" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        {!! Form::open(['id' => 'editOrderInvoice', 'route' => 'orders.editOrderInvoice']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit invoice</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('bank_account_number', 'Invoice No.', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('bank_account_number', null, ['class' => 'form-control', 'required']) !!}
                    <span class="invalid-feedback" role="alert">
                        <strong id="alert_bank_account_number"></strong>
                    </span>
                    <span class="number-new" role="alert">
                        New number: {{$invoiceBankAccountNo}}
                    </span>
                    {!! Form::hidden('bank_account_number', null, ['id' => 'default_bank_account_number']) !!}
                    {!! Form::hidden('invoice_id', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('invoice_date', 'Invoice Date', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('invoice_date', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('invoice_percent', 'Invoice percent', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('invoice_percent', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('invoice_amount', 'Invoice amount', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('invoice_amount', null, ['class' => 'form-control', 'required']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Generate', ['class' => 'btn btn-primary']) !!}
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
