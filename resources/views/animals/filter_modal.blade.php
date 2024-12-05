<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter animals" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'animals.filterAnimals', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter animals</h5>
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
                                    {!! Form::label('species_by_name', 'Species by name') !!}
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
            <div class="row mb-3">
                <div class="col-md-3">
                    {!! Form::label('filter_class_id', 'Class *') !!}
                    {!! Form::select('filter_class_id', $classes,  null, ['id' => 'filter_class_id', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_order_id', 'Order *') !!}
                    {!! Form::select('filter_order_id', array(), null, ['id' => 'filter_order_id', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_family_id', 'Family *') !!}
                    {!! Form::select('filter_family_id', array(), null, ['id' => 'filter_family_id', 'class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('filter_genus_id', 'Genus *') !!}
                    {!! Form::select('filter_genus_id', array(), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('has_spanish_name', 'Has spanish name: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_spanish_name', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_spanish_name', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_spanish_name', 'no') !!}
                    {!! Form::label('not_set', 'not set') !!}
                </div>
                <div class="col-md-3 text-right">
                    {!! Form::label('has_crates', 'Has crates: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_crates', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_crates', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_crates', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('in_standard_list', 'In standard list: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_in_standard_list', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_standard_list', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_standard_list', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-3 text-right">
                    {!! Form::label('has_catalog_picture', 'Has catalog picture: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_catalog_picture', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_catalog_picture', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_catalog_picture', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('in_standard_list_public', 'In standard list public: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3 text-center">
                    {!! Form::radio('filter_in_standard_list_public', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_standard_list_public', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-3 text-right">
                    {!! Form::label('has_pictures', 'Has pictures: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_pictures', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_pictures', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_pictures', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('in_surplus_of_suppliers', 'In surplus of suppliers: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_in_surplus_of_suppliers', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_surplus_of_suppliers', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_surplus_of_suppliers', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-3 text-right">
                    {!! Form::label('has_pictures_less_10kb', 'Has pictures less 10Kb: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_pictures_less_10kb', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_pictures_less_10kb', 'yes') !!}
                    {!! Form::label('yes', 'Yes') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('in_standard_wanted', 'In standard wanted: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_in_standard_wanted', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_standard_wanted', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_standard_wanted', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-3 text-right">
                    {!! Form::label('has_more_than_10_pictures', 'Has more than 10 pictures: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_more_than_10_pictures', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_more_than_10_pictures', 'yes') !!}
                    {!! Form::label('yes', 'Yes') !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 text-right">
                    {!! Form::label('in_wanted_of_clients', 'In wanted of clients: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_in_wanted_of_clients', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_wanted_of_clients', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_wanted_of_clients', 'no') !!}
                    {!! Form::label('no', 'no') !!}
                </div>
                <div class="col-md-3 text-right">
                    {!! Form::label('has_wrong_size_catalog_pic', 'Has wrong size catalog picture: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_has_wrong_size_catalog_pic', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_has_wrong_size_catalog_pic', 'yes') !!}
                    {!! Form::label('yes', 'Yes') !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 text-right">
                    {!! Form::label('in_offers', 'In offers: ', ['class' => 'font-weight-bold mr-2']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::radio('filter_in_offers', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_offers', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_in_offers', 'no') !!}
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
