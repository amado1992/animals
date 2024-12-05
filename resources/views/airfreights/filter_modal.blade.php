<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter airfreights" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['route' => 'airfreights.filterAirfreights', 'method' => 'GET']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter airfreights</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('source', 'Source', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_source', ['offer' => 'Offer', 'estimation' => 'Estimation'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-8">
                    {!! Form::label('tranport_agent', 'Transport agent', ['class' => 'font-weight-bold']) !!}
                    <select class="contact-select2 form-control" type="default" style="width: 100%" name="filter_tranport_agent_id"></select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('from_continent', 'Departure continent', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_from_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('to_continent', 'Arrival continent', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('filter_to_continent', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    {!! Form::label('type', 'Select option: ', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    {!! Form::radio('filter_type', 'all', true) !!}
                    {!! Form::label('all', 'All', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_type', 'not_set') !!}
                    {!! Form::label('not_set', 'Not set', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_type', 'volKg') !!}
                    {!! Form::label('per_volKg', 'Per vol.kg', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_type', 'lowerdeck') !!}
                    {!! Form::label('lowerdeck_pallet', 'Lowerdeck pallet', ['class' => 'mr-2']) !!}
                    {!! Form::radio('filter_type', 'maindeck') !!}
                    {!! Form::label('maindeck_pallet', 'Maindeck pallet') !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! Form::label('offered_date', 'Offered date', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('start_date', 'Start date') !!}
                    {!! Form::date('filter_start_offered_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('end_date', 'End date') !!}
                    {!! Form::date('filter_end_offered_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    {!! Form::label('date_modified', 'Date modified', ['class' => 'font-weight-bold']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('start_date', 'Start date') !!}
                    {!! Form::date('filter_start_modified_date', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('end_date', 'End date') !!}
                    {!! Form::date('filter_end_modified_date', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('intern_remarks', 'Remarks', ['class' => 'font-weight-bold']) !!}
                    {!! Form::text('filter_remarks', null, ['class' => 'form-control']) !!}
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
