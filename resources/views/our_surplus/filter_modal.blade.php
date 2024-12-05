<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter standard surplus" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'our-surplus.filterOurSurplus', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter standard surplus</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-8">
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
                                            <select class="animal-select2 form-control" type="filter_std_surplus" style="width: 100%" name="filter_animal_id"></select>
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
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                {!! Form::label('has_spanish_name', 'Has spanish name:', ['class' => 'font-weight-bold mr-2']) !!}
                            </div>
                            <div>
                                {!! Form::select('filter_has_spanish_name', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            {!! Form::label('filter_availability', 'Availability:', ['class' => 'font-weight-bold mr-2']) !!}
                            {!! Form::select('filter_availability', Arr::prepend($availability, 'Empty', 'empty'), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
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
                            {!! Form::label('is_public', 'On website', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('filter_is_public', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('origin', 'Origin', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('filter_origin', $origin, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('have_cost_prices', 'Has cost prices', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('filter_have_cost_prices', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::label('have_sale_prices', 'Has sales prices', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('filter_have_sale_prices', $confirm_options, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            {!! Form::label('region', 'Region', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('filter_region', Arr::prepend($regionsNames->toArray(), 'Empty', 0), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::label('area', 'Area', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('filter_area', Arr::prepend($areas->pluck('name', 'id')->toArray(), 'Empty', 0), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            {!! Form::label('imagen_species', 'General images: ',['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-9" style="margin: 0 0 0 -20px;">
                            {!! Form::radio('filter_imagen_species', 'yes') !!}
                            {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                            {!! Form::radio('filter_imagen_species', 'no') !!}
                            {!! Form::label('no', 'no') !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3">
                            {!! Form::label('upload_images', 'Uploaded images: ',['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-9" style="margin: 0 0 0 -8px;">
                            {!! Form::radio('filter_upload_images', 'yes') !!}
                            {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                            {!! Form::radio('filter_upload_images', 'no') !!}
                            {!! Form::label('no', 'no') !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3" style="margin: 0 -33px 0 0;">
                            {!! Form::label('show_stuffed', 'Show Stuffed: ',['class' => 'font-weight-bold']) !!}
                        </div>
                        <div class="col-md-9">
                            {!! Form::radio('filter_show_stuffed', 'yes') !!}
                            {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                            {!! Form::radio('filter_show_stuffed', 'no') !!}
                            {!! Form::label('no', 'no') !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            {!! Form::label('date_modified_period', 'Date modified period', ['class' => 'font-weight-bold']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6 text-center">
                            {!! Form::label('updated_at_from', 'Start date', ['class' => 'font-weight-bold']) !!}
                            {!! Form::date('filter_updated_at_from', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6 text-center">
                            {!! Form::label('updated_at_to', 'End date', ['class' => 'font-weight-bold']) !!}
                            {!! Form::date('filter_updated_at_to', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            {!! Form::label('intern_remarks', 'Intern remarks', ['class' => 'font-weight-bold']) !!}
                            {!! Form::text('filter_intern_remarks', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            {!! Form::label('areas_to_offer', 'Areas to offer:', ['class' => 'font-weight-bold']) !!}<br>
                            <div class="checkbox"><label>{!! Form::checkbox('filter_areas_empty', 0) !!} Empty</label></div>
                            <div id="filter-areas">
                                @foreach($areas as $area)
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('filter_area_id[]', $area->id) !!} {{$area->short_cut}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::label('same_surplus', 'Search same records by species, origin and area continent:', ['class' => 'font-weight-bold']) !!}<br>
                        </div>
                        <div class="col-md-8 offset-md-2">
                            {!! Form::checkbox('filter_same_surplus', null) !!}
                            {!! Form::label('filter_same_surplus', 'Same surplus') !!}
                        </div>
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
