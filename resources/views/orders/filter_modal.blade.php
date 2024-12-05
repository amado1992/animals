<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter orders" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'orders.filterOrders', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter orders</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('order_number', 'Order number', ['class' => 'font-weight-bold']) !!}
                    {!! Form::number('filter_order_number', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('order_year', 'Order year', ['class' => 'font-weight-bold']) !!}
                    {!! Form::selectYear('filter_order_year', 2000, 2050, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('realized_year', 'Realized year', ['class' => 'font-weight-bold']) !!}
                    {!! Form::selectYear('filter_realized_year', 2000, 2050, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('filter_project_manager', 'Project manager', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_project_manager', $admins, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('filter_order_company', 'Company', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_order_company', $companies, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('animal', 'Species', ['class' => 'font-weight-bold']) !!}
                    <select class="animal-select2 form-control" type="filter_order_species" style="width: 100%" name="filter_animal_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('client', 'Client', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="filter_order_client" style="width: 100%" name="filter_client_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('supplier', 'Supplier', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="filter_order_supplier" style="width: 100%" name="filter_supplier_id"></select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('ordered_on', 'Ordered on:', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('start_date', 'Start date') !!}
                    {!! Form::date('filter_start_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('end_date', 'End date') !!}
                    {!! Form::date('filter_end_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_intern_remarks', null, ['class' => 'form-control']) !!}
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
