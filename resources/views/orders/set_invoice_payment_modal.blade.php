<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Set invoice payment" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'orders.setOrderInvoicePayment']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Set invoice payment</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('paid_value', 'Paid amount', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('paid_value', null, ['class' => 'form-control']) !!}
                    {!! Form::hidden('invoice_id', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('banking_cost', 'Banking cost', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('banking_cost', null, ['class' => 'form-control']) !!}
                    {!! Form::hidden('invoice_amount', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-8 offset-2">
                    {!! Form::label('paid_date', 'Paid on', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('paid_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 offset-2">
                    {!! Form::label('payment_type', 'Payment type', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('payment_type', $payment_type, null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
