<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter contacts" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'contacts.filterContacts', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter contacts</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2 align-items-end">
                <div class="col-md-2">
                    {!! Form::label('filter_title', 'Title') !!}
                    {!! Form::select('filter_title', ['empty' => 'Empty', 'Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('filter_name', 'First name/Last name') !!}
                    {!! Form::text('filter_name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_mobile_phone', 'Mobile phone') !!}
                    {!! Form::text('filter_mobile_phone', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-2 text-right">
                    {!! Form::checkbox('filter_deleted', null) !!}
                    {!! Form::label('filter_deleted', 'Deleted') !!}
                </div>
            </div>
            <div class="row mb-2 align-items-end">
                <div class="col-md-2"></div>
                <div class="col-md-4">
                    {!! Form::checkbox('filter_name_empty', null) !!}
                    {!! Form::label('filter_name_empty', 'Empty') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::checkbox('filter_mobile_empty', null) !!}
                    {!! Form::label('filter_mobile_empty', 'Empty') !!}
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="row mb-2 align-items-end">
                <div class="col-md-4">
                    {!! Form::label('filter_email', 'Email') !!}
                    {!! Form::text('filter_email', null, ['id' => 'filter_email', 'class' => 'form-control autocomplete', 'placeholder' => '- search email -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_institution_type', 'Institution type') !!}
                    {!! Form::select('filter_institution_type', Arr::prepend($organization_types, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_institution_name', 'Institution name') !!}
                    {!! Form::text('filter_institution_name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('filter_institution_level', 'Level') !!}
                    {!! Form::select('filter_institution_level', Arr::prepend($organization_levels, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2 align-items-end">
                <div class="col-md-4">
                    {!! Form::checkbox('filter_email_empty', null) !!}
                    {!! Form::label('filter_email_empty', 'Empty') !!}
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    {!! Form::checkbox('filter_institution_empty', null) !!}
                    {!! Form::label('filter_institution_empty', 'Institution empty') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    {!! Form::label('filter_city', 'City') !!}
                    {!! Form::text('filter_city', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_country', 'Country') !!}
                    {!! Form::select('filter_country', Arr::prepend($countries->toArray(), 'Empty', 0), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_continent', 'Continent') !!}
                    {!! Form::select('filter_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_mailing_category', 'Mailing category') !!}
                    {!! Form::select('filter_mailing_category', Arr::prepend($mailing_categories, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    {!! Form::checkbox('filter_city_empty', null) !!}
                    {!! Form::label('filter_city_empty', 'Empty') !!}
                </div>
            </div>
            <hr>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('filter_relation_type', 'Relation type: ') !!}
                </div>
                <div class="col-md-6">
                    {!! Form::radio('filter_relation_type', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_relation_type', 'both') !!}
                    {!! Form::label('both', 'Both', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_relation_type', 'supplier') !!}
                    {!! Form::label('supplier', 'Supplier', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_relation_type', 'client') !!}
                    {!! Form::label('client', 'Client', ['class' => 'mr-2']) !!}
                </div>
            </div>
            <hr>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('is_member', 'Is member: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_is_member', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_is_member', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_is_member', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('is_active', 'Is active: ') !!}
                </div>
                <div class="col-md-4">
                    {!! Form::radio('filter_is_active', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_is_active', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_is_active', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <hr>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('has_requests', 'Has offers: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_requests', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_requests', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_requests', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('has_orders', 'Has orders: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_orders', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_orders', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_orders', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    {!! Form::label('has_invoices', 'Has invoices: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_invoices', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_invoices', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_invoices', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('include_species', 'Include species: ') !!}
                </div>
                <div class="col-md-5">
                    <select class="animal-select2 form-control" type="default" style="width: 100%" name="filter_animal_id"></select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
            <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
