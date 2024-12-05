<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => 'animals.assignCratesToSpecies']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assign crates to species</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        {!! Form::label('iata_code', 'IATA', ['class' => 'font-weight-bold']) !!}
                        {!! Form::number('iata_code', '0', ['class' => 'form-control']) !!}
                        {!! Form::hidden('animal_id', $animalId, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-9">
                        {!! Form::label('crates_list', 'Crates list', ['class' => 'font-weight-bold']) !!}
                        {!! Form::select('crates_list[]', [], null, ['id' => 'crates_list', 'class' => 'form-control', 'multiple']) !!}
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
