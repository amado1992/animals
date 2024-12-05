
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => 'offers.saveSelectedSpeciesAirfreightsValues']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Set airfreights values</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('airfreight_cost_value', 'Cost value', ['class' => 'font-weight-bold']) !!}
                            {!! Form::number('airfreight_cost_value', 0, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('airfreight_sales_value', 'Sales value', ['class' => 'font-weight-bold']) !!}
                            {!! Form::number('airfreight_sales_value', 0, ['class' => 'form-control']) !!}
                        </div>
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
