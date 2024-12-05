<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Select country" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        {!! Form::open(['id' => 'selectContinentCountryForMailing']) !!}

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Select country for mailing</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="d-inline align-items-center">
                        {!! Form::label('email_body_text', 'Standard text: ', ['class' => 'font-weight-bold mr-2']) !!}
                        {!! Form::radio('select_body_text', 'search_mail', true) !!}
                        {!! Form::label('search_mail', 'Search mail', ['class' => 'mr-2']) !!}
                        {!! Form::radio('select_body_text', 'direct_project') !!}
                        {!! Form::label('direct_project', 'Direct project', ['class' => 'mr-2']) !!}
                        {!! Form::radio('select_body_text', 'export_addresses') !!}
                        {!! Form::label('export_addresses', 'Export addresses') !!}
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    {!! Form::label('select_area', 'Area', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('select_area', $areas, null, ['class' => 'form-control', 'placeholder' => '- select area -']) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('select_country', 'Country', ['class' => 'font-weight-bold']) !!}
                    {!! Form::select('select_country', $to_country, null, ['class' => 'form-control', 'placeholder' => '- select country -']) !!}
                    {!! Form::hidden('triggered_from', null, ['class' => 'form-control']) !!}
                    {!! Form::hidden('triggered_id', null, ['class' => 'form-control']) !!}
                    {!! Form::hidden('search_mailing_id', null, ['class' => 'form-control']) !!}
                    {!! Form::hidden('animal_id', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            {!! Form::submit('Go to mailing', ['class' => 'btn btn-primary']) !!}
            <button type="reset" class="btn btn-secondary">Reset</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
        </div>
    </div>
</div>
