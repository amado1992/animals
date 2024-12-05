<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected institutions" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'organisations.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected institutions</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('organisation_type', 'Type') !!}
                    {!! Form::select('organisation_type', $organization_types, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('level', 'Level') !!}
                    {!! Form::select('level', $organization_levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('country', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    {!! Form::label('city', 'City') !!}
                    {!! Form::text('city', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('mailing_category', 'Mailing category') !!}
                    {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4" id="canonical_name">
                    {!! Form::label('canonical_name_type', 'Canonical Name*') !!}
                    <div class="frmSearch">
                        <div class="invalid-feedback-tooltips d-none">
                            <span id="invalid-canonical_name" role="alert">
                            </span>
                            <div class="invalid-arrow">
                            </div>
                        </div>
                        {!! Form::text('canonical_name', null, ['class' => 'form-control', 'id' => 'search-box' , 'autocomplete' => 'off', 'data-validate' => "true"]) !!}
                        <div id="suggesstion-box" class="d-none"></div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label>{!! Form::checkbox('makeInstitutionNameBasedOnCityAndType', 0) !!} Make institution name: City + Type of institution</label>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-12">
                    <label>{!! Form::checkbox('makeWebsiteFromEmailDomain', 0) !!} Make website url based on email domain</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! Form::label('associations', 'Associations') !!}<br>
                    @foreach($associations as $association)
                        <label class="checkbox-inline mr-2">
                            {!! Form::checkbox('association', $association->key) !!} {{$association->label}}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save institutions', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
