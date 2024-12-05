<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Edit selected contacts" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        {!! Form::open(['route' => 'wanted.editSelectedRecords']) !!}

        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit selected contacts</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
            <div class="row mb-2">
                <div class="col-md-2">
                    {!! Form::label('title', 'Title') !!}
                    {!! Form::select('title', ['Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::label('first_name', 'First name') !!}
                    {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-5">
                    {!! Form::label('last_name', 'Last name') !!}
                    {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('institution', 'Institution') !!}
                    <select class="institution-select2 form-control" type="default" style="width: 100%" name="institution_id"></select>
                </div>
                <div class="col-md-6">
                    {!! Form::label('institution_name', 'Institution name') !!}
                    {!! Form::text('institution_name', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::label('institution_level', 'Level') !!}
                    {!! Form::select('institution_level', $organization_levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"></div>
                <div class="col-md-6">
                    <label>{!! Form::checkbox('makeInstitutionNameBasedOnCityAndType', 0) !!} Make institution name: City + Type of institution</label>
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4">
                    {!! Form::label('city', 'City') !!}
                    {!! Form::text('city', null, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('country', 'Country') !!}
                    {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('mailing_category', 'Mailing category') !!}
                    {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <label>{!! Form::checkbox('makeCountryBasedOnEmailExtension', 0) !!} Make country based on email extension</label>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>

        <div class="modal-footer">
          {!! Form::submit('Save contacts', ['id' => 'sendEditSelectionForm', 'class' => 'btn btn-primary']) !!}
          <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
      </div>
    </div>
</div>
