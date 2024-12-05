
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            {!! Form::open(['route' => 'offers.saveOfferAirfreightPallet']) !!}

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Select airfreight pallet</h5>
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
                            {!! Form::hidden('offer_id', null, ['class' => 'form-control']) !!}
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
                    </div>
                </div>
            </div>

            <div class="modal-footer" id="saveOfferAirfreightPalletSave">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
