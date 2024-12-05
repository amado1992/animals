<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter offers" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'offers.filterOffers', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter offers</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('request_number', 'Request number', ['class' => 'font-weight-bold']) !!}
                    {!! Form::number('filter_request_number', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('price_type', 'Price type', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_price_type', $price_type, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('to_remind', 'To remind', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_to_remind', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('animal', 'Species', ['class' => 'font-weight-bold']) !!}
                    <select class="animal-select2 form-control" type="filter_offer_species" style="width: 100%" name="filter_animal_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('client', 'Client', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="filter_offer_client" style="width: 100%" name="filter_client_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('supplier', 'Supplier', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="filter_offer_supplier" style="width: 100%" name="filter_supplier_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('manager', 'Manager', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_manager_id', $admins, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('requested_on', 'Requested on:', ['class' => 'font-weight-bold']) !!}
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
                    {!! Form::label('intern_remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_intern_remarks', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('inquiries', 'Show inquiries from website: ',['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::radio('filter_inquiries', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_inquiries', 'no') !!}
                    {!! Form::label('no', 'no') !!}
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
