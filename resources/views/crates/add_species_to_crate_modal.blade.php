<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {!! Form::open() !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add species to crate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-10">
                        {!! Form::label('select_species', 'Species', ['class' => 'font-weight-bold']) !!}
                        <select class="animal-select2 form-control" type="default" style="width: 100%" name="animal_id"></select>
                        {!! Form::hidden('crate_id', $crateId, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-2 align-self-end">
                        <a href="#" id="tempSelectedSpecies" class="btn btn-light">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                            {!! Form::label('selected_species', 'Selected species:', ['class' => 'font-weight-bold']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="selectedSpecies" class="table table-striped table-sm mb-0">
                            <thead>
                                <tr style="text-align: center;">
                                    <th></th>
                                    <th>Species</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
      </div>
  </div>
