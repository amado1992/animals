
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => 'offers.saveOfferSpeciesAirfreights']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Select species airfreight</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('departure_continent', 'Departure continent', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('departure_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                            {!! Form::hidden('offer_species_id', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('arrival_continent', 'Arrival continent', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('arrival_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('freights', 'Freights', ['class' => 'font-weight-bold']) !!}
                            {!! Form::select('freights', array(), null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                        </div>
                    </div>
                    <div class="col-md-2 align-self-center mt-2">
                        <a href="#" class="btn btn-sm btn-light" id="getAirfreights"><i class="fas fa-fw fa-sync"></i></a>
                        <a href="#" class="btn btn-sm btn-light" id="tempAddSelectedAirfreight"><i class="fas fa-fw fa-plus"></i>Add</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! Form::label('selected_airfreights', 'Selected airfreights:', ['class' => 'font-weight-bold']) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="selectedAirfreights" class="table table-striped table-sm mb-0">
                            <thead>
                                <tr style="text-align: center;">
                                    <th></th>
                                    <th style="width: 150px;">Departure continent</th>
                                    <th style="width: 150px;">Arrival continent</th>
                                    <th style="width: 100px;">Type</th>
                                    <th style="width: 50px;">Currency</th>
                                    <th style="width: 100px;">Price</th>
                                    <th style="width: 150px;">Remarks</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="buttonSpeciesAirfreights">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
      </div>
  </div>
