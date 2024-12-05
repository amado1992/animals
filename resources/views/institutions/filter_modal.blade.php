<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter institutions" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'organisations.filterOrganizations', 'method' => 'GET']) !!}

        @foreach ($filterDataKeyVal as $filterKey => $filterValue)
           {!! Form::hidden($filterKey, $filterValue) !!}
        @endforeach

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter institutions & Contacts</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2" style="margin-bottom: -0.5em !important">
               <div class="col-md-2">
                     {!! Form::radio('filter_model_type', 'B', true, ['id' => 'modelB']) !!}
                     {!! Form::label('B', 'Both', ['class' => 'mr-2']) !!}
                  <br/>{!! Form::radio('filter_model_type', 'C', false, ['id' => 'modelC']) !!}
                     {!! Form::label('C', 'Contacts', ['class' => 'mr-2']) !!}
                  <br />{!! Form::radio('filter_model_type', 'I', false, ['id' => 'modelI']) !!}
                     {!! Form::label('I', 'Institutions', ['class' => 'mr-2']) !!}
               </div>
                <div class="col-md-2">
                    {!! Form::label('filter_organisation_type', 'Type', ['class' => 'hidecontact']) !!}
                    {!! Form::select('filter_organisation_type', $organization_types, null, ['class' => 'form-control hidecontact', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_name', 'Name') !!}
                    {!! Form::text('filter_name', ($filterDataKeyVal['hidden_filter_name'] ?? ''), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3 only-contacts" style="display: none">
                    {!! Form::label('filter_name', 'Institution Name') !!}
                    {!! Form::text('filter_institution_name', ($filterDataKeyVal['hidden_filter_institution_name'] ?? ''), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3 hidecontact">
                    {!! Form::label('filter_name', 'Canonical Name') !!}
                    {!! Form::text('filter_canonical_name', ($filterDataKeyVal['hidden_filter_canonical_name'] ?? ''), ['class' => 'form-control hidecontact']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('filter_level', 'Level', ['class' => 'hidecontact']) !!}
                    {!! Form::select('filter_level', Arr::prepend($organization_levels, 'Empty', 'empty'), null, ['class' => 'form-control hidecontact', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2 align-items-end">
                <div class="col-md-4"></div>
                <div class="col-md-3">
                    {!! Form::checkbox('filter_name_empty', null, false) !!}
                    {!! Form::label('filter_name_empty', 'Empty') !!}
                </div>
                <div class="col-md-3 only-contacts">
                    {!! Form::checkbox('filter_institution_name_empty', null, false) !!}
                    {!! Form::label('filter_institution_name_empty', 'Empty') !!}
                </div>
                <div class="col-md-3 hidecontact">
                    {!! Form::checkbox('filter_canonical_name_empty', null, false, ['class' => 'hidecontact']) !!}
                    {!! Form::label('filter_canonical_name_empty', 'Empty', ['class' => 'hidecontact']) !!}
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('filter_email', 'Email') !!}
                    {!! Form::text('filter_email', ($filterDataKeyVal['hidden_filter_email'] ?? ''), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('filter_domain_name', 'Domain name') !!}
                    {!! Form::text('filter_domain_name', ($filterDataKeyVal['hidden_filter_domain_name'] ?? ''), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('filter_phone', 'Phone') !!}
                    {!! Form::text('filter_phone', ($filterDataKeyVal['hidden_filter_phone'] ?? ''), ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2 align-items-end">
                <div class="col-md-4">
                    {!! Form::checkbox('filter_email_empty', null, 'on', ['id' => 'filter_email_empty']) !!}
                    {!! Form::label('filter_email_empty', 'Empty') !!}
                </div>
                <div class="col-md-4">
                    {!! Form::checkbox('filter_domain_name_empty', null) !!}
                    {!! Form::label('filter_domain_name_empty', 'Empty') !!}
                </div>
                <div class="col-md-4">
                    {!! Form::checkbox('filter_phone_empty', null) !!}
                    {!! Form::label('filter_phone_empty', 'Empty') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('filter_city', 'City') !!}
                    {!! Form::text('filter_city', ($filterDataKeyVal['hidden_filter_city'] ?? ''), ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('filter_country', 'Country') !!}
                    {!! Form::select('filter_country_id', Arr::prepend($countries->toArray(), 'Empty', 0), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('filter_continent', 'Continent') !!}
                    {!! Form::select('filter_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
           <div class="extraline" style="display: none;height:1.7em"> </div>
            <div class="row mb-2 align-items-end">
                <div class="col-md-4">
                    {!! Form::checkbox('filter_city_empty', null, false, ['class' => 'hidecontact']) !!}
                    {!! Form::label('filter_city_empty', 'Empty', ['class' => 'hidecontact']) !!}
                </div>
            </div>
            <div class="row mb-2 align-items-center">
                <div class="col-md-2">
                    {!! Form::label('has_website', 'Has website: ', ['class' => 'hidecontact']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_website', 'all', true, ['class' => 'hidecontact']) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2 hidecontact']) !!}
                    {!! Form::radio('filter_has_website', 'yes', false, ['class' => 'hidecontact']) !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2 hidecontact']) !!}
                    {!! Form::radio('filter_has_website', 'no', false, ['class' => 'hidecontact']) !!}
                    {!! Form::label('no', 'no', ['class' => 'hidecontact']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::label('filter_website', 'Website', ['class' => 'hidecontact']) !!}
                    {!! Form::text('filter_website', ($filterDataKeyVal['hidden_filter_website'] ?? ''), ['class' => 'form-control hidecontact']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('filter_association', 'Association') !!}
                    {!! Form::select('filter_association', Arr::prepend($associations->pluck('label', 'key')->toArray(), 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('filter_vat_number', 'Vat number', ['class' => 'hidecontact']) !!}
                    {!! Form::text('filter_vat_number', ($filterDataKeyVal['hidden_filter_vat_number'] ?? ''), ['class' => 'form-control hidecontact']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::label('filter_remarks', 'Remarks', ['class' => 'hidecontact']) !!}
                    {!! Form::text('filter_remarks', ($filterDataKeyVal['hidden_filter_remarks'] ?? ''), ['class' => 'form-control hidecontact']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_mailing_category', 'Mailing category') !!}
                    {!! Form::select('filter_mailing_category', Arr::prepend($mailing_categories, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row align-items-end">
                <div class="col-md-4">
                    {!! Form::checkbox('filter_vat_empty', null, false, ['class' => 'hidecontact']) !!}
                    {!! Form::label('filter_vat_empty', 'Empty', ['class' => 'hidecontact']) !!}
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
                    {!! Form::label('has_surplus', 'Has surplus: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_surplus', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_surplus', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_surplus', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('has_wanted', 'Has wanted: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_wanted', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_wanted', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_wanted', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
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
                    {!! Form::label('has_collection', 'Has collections: ') !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_collection', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_collection', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_collection', 'no') !!}
                    {!! Form::label('no', 'no') !!}
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
