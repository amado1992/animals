<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter surplus" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'surplus.filterSurplus', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter surplus</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-10">
                    <div class="card shadow">
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-md-12">
                                    {!! Form::radio('filter_animal_option', 'by_id', true) !!}
                                    {!! Form::label('species_by_id', 'Search species', ['class' => 'mr-2']) !!}
                                    {!! Form::radio('filter_animal_option', 'by_name') !!}
                                    {!! Form::label('species_by_name', 'Species by name', ['class' => 'mr-2']) !!}
                                    {!! Form::radio('filter_animal_option', 'empty') !!}
                                    {!! Form::label('empty_species', 'Empty') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="animal-select2 form-control" type="filter_surplus_supplier" style="width: 100%" name="filter_animal_id"></select>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::text('filter_animal_name', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5">
                    {!! Form::label('supplier_institution', 'Supplier institution', ['class' => 'font-weight-bold mr-3']) !!}
                    {!! Form::checkbox('empty_institution') !!}
                    {!! Form::label('empty_institution', 'Empty') !!}
                    <select class="institution-select2 form-control" type="filter_surplus_institution" style="width: 100%" name="filter_institution_id"></select>
                </div>
                <div class="col-md-5">
                    {!! Form::label('supplier_contact', 'Supplier contact', ['class' => 'font-weight-bold mr-3']) !!}
                    {!! Form::checkbox('empty_contact', null) !!}
                    {!! Form::label('empty_contact', 'Empty') !!}
                    <select class="contact-select2 form-control" type="default" style="width: 100%" name="filter_supplier_id"></select>
                </div>
                <div class="col-md-2">
                    {!! Form::label('filter_institution_level', 'Level', ['class' => 'font-weight-bold mr-3']) !!}
                    {!! Form::select('filter_institution_level', Arr::prepend($organization_levels, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    {!! Form::label('animal_class', 'Class', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_animal_class', $classes, null, ['id' => 'filter_animal_class', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('animal_order', 'Order', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_animal_order', array(), null, ['id' => 'filter_animal_order', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('animal_family', 'Family', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_animal_family', array(), null, ['id' => 'filter_animal_family', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('animal_genus', 'Genus', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_animal_genus', array(), null, ['id' => 'filter_animal_genus', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    {!! Form::label('origin', 'Origin', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_origin', $origin, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('country', 'Country', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_country', Arr::prepend($countries->toArray(), 'Empty', 0), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('area', 'Area', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_area', Arr::prepend($areas->toArray(), 'Empty', 0), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('surplus_status', 'Status', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_surplus_status', Arr::prepend($surplus_status, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    {!! Form::label('have_cost_prices', 'Have cost prices', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_have_cost_prices', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('have_sale_prices', 'Have sales prices', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_have_sale_prices', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('have_standard_surplus', 'Standard surplus', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_have_standard_surplus', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('to_members', 'To members', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_to_members', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    {!! Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_remarks', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_intern_remarks', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('imagen_species', 'General images: ',['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-10">
                    {!! Form::radio('filter_imagen_species', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_imagen_species', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('upload_images', 'Uploaded images: ',['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-10">
                    {!! Form::radio('filter_upload_images', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_upload_images', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('show_stuffed', 'Show Stuffed: ',['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-10">
                    {!! Form::radio('filter_show_stuffed', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_show_stuffed', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 text-center">
                    {!! Form::label('date_modified_period', 'Date modified period', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 text-center">
                    {!! Form::label('updated_at_from', 'Start date', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('filter_updated_at_from', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3 text-center">
                    {!! Form::label('updated_at_to', 'End date', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('filter_updated_at_to', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6">
                    <div class="d-flex">
                        {!! Form::checkbox('filter_same_surplus', null) !!}
                        {!! Form::label('same_surplus', 'Search same records by: species, institution, origin, and area continent', ['class' => 'font-weight-bold ml-2']) !!}
                    </div>
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
