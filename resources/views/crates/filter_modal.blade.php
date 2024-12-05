<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter crates" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'crates.filterCrates', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter crates</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('crate_name', 'Crate name', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_crate_name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-8">
                    {!! Form::label('filter_crate_animal', 'Crate species', ['class' => 'font-weight-bold']) !!}
                    <select class="animal-select2 form-control" type="default" style="width: 100%" name="filter_crate_animal_id"></select>
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
            <div class="row mb-3">
                <div class="col-md-4">
                    {!! Form::label('crate_iata_number', 'Crate iata No.', ['class' => 'font-weight-bold']) !!}
                    {!! Form::number('filter_crate_iata_number', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('crate_has_files', 'Has documents: ', ['class' => 'font-weight-bold mr-2']) !!}
                    {!! Form::radio('filter_crate_has_files', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_crate_has_files', 'yes') !!}
                    {!! Form::label('yes', 'Yes', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_crate_has_files', 'no') !!}
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
