<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter wanteds" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'wanted.filterWanted', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter wanteds</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-12">
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
                                    <select class="animal-select2 form-control" type="default" style="width: 100%" name="filter_animal_id"></select>
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
                <div class="col-md-6">
                    {!! Form::label('client_institution', 'Client institution', ['class' => 'font-weight-bold mr-3']) !!}
                    {!! Form::checkbox('empty_institution') !!}
                    {!! Form::label('empty_institution', 'Empty') !!}
                    <select class="institution-select2 form-control" type="filter_wanted_institution" style="width: 100%" name="filter_institution_id"></select>
                </div>
                <div class="col-md-6">
                    {!! Form::label('client_contact', 'Client contact', ['class' => 'font-weight-bold mr-3']) !!}
                    {!! Form::checkbox('empty_client', null) !!}
                    {!! Form::label('empty_client', 'Empty') !!}
                    <select class="contact-select2 form-control" type="default" style="width: 100%" name="filter_client_id"></select>
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
                    {!! Form::label('filter_institution_level', 'Level', ['class' => 'font-weight-bold mr-3']) !!}
                    {!! Form::select('filter_institution_level', Arr::prepend($levels, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('country', 'Country', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_country', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_continent', 'Continent', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('area', 'Area', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_area', $areas, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-5">
                    {!! Form::label('have_standard_wanted', 'Have standard wanted', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_have_standard_wanted', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_remarks', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_intern_remarks', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! Form::label('date_modified_period', 'Date modified period', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 text-center">
                    {!! Form::label('updated_at_from', 'From', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('filter_updated_at_from', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6 text-center">
                    {!! Form::label('updated_at_to', 'To', ['class' => 'font-weight-bold']) !!}
                    {!! Form::date('filter_updated_at_to', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2" style="margin: 16px 0 0 0;">
                <div class="col-md-4">
                    {!! Form::label('imagen_species', 'Imagen Species: ',['class' => 'font-weight-bold']) !!}
                </div>
                <div class="col-md-8" style="margin: 0 0 0 -32px;">
                    {!! Form::radio('filter_imagen_species', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_imagen_species', 'no') !!}
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
