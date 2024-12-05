<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter offers" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'invoices.filterInvoices', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter invoices</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-3">
                    {!! Form::label('filter_invoice_year', 'Invoice year', ['class' => 'font-weight-bold']) !!}
                    {!! Form::selectYear('filter_invoice_year', 2000, 2050, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_invoice_month', 'Invoice month', ['class' => 'font-weight-bold']) !!}
                    {!! Form::selectMonth('filter_invoice_month', null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_order_year', 'Order year', ['class' => 'font-weight-bold']) !!}
                    {!! Form::selectYear('filter_order_year', 2000, 2050, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_order_status', 'Order status', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_order_status', $orderStatuses, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5">
                    {!! Form::label('filter_invoice_species', 'Species', ['class' => 'font-weight-bold']) !!}
                    <select class="animal-select2 form-control" type="default" style="width: 100%" name="filter_invoice_species_id"></select>
                </div>
                <div class="col-md-5">
                    {!! Form::label('filter_invoice_contact', 'Contact person', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="default" style="width: 100%" name="filter_invoice_contact_id"></select>
                </div>
                <div class="col-md-2">
                    {!! Form::label('price_type', 'Price type', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_price_type', $price_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    {!! Form::label('filter_invoice_company', 'Company', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_invoice_company', $companies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_bank_account', 'Bank account', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_bank_account_id', array(), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_invoice_payment_type', 'Payment type', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_invoice_payment_type', $invoice_payment_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_invoice_type', 'Invoice type', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_invoice_type', $invoice_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('filter_paid_value', 'Paid:', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::radio('filter_paid_value', 'any', true); !!}
                    {!! Form::label('paid_any', 'Any') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::radio('filter_paid_value', 'yes'); !!}
                    {!! Form::label('paid_yes', 'Yes') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::radio('filter_paid_value', 'no'); !!}
                    {!! Form::label('paid_no', 'No') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('filter_banking_cost', 'Banking cost:', ['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::radio('filter_banking_cost', 'any', true); !!}
                    {!! Form::label('banking_cost_any', 'Any') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::radio('filter_banking_cost', 'ok'); !!}
                    {!! Form::label('banking_cost_ok', 'Ok') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::radio('filter_banking_cost', 'not_set'); !!}
                    {!! Form::label('banking_cost_not_set', 'Not set') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_banking_cost', 'amount_missing'); !!}
                    {!! Form::label('banking_cost_amount_missing', 'Amount missing') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('filter_invoice_remark', 'Remark', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_invoice_remark', null, ['id' => 'filter_invoice_remark', 'class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
            <button type="reset" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
