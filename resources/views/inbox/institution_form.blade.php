
<div class="alert alert-danger alert-important d-none">

</div>

<div id="countryMessage" class="alert alert-warning" role="warning">
    Please check if the name of the country can be located, see also website or email extension In case there is absolutely no country, then insert the institution without it.
</div>

<div class="row">
    <div class="col-md-6">
        {!! Form::label('email', 'Email address') !!}
        {!! Form::text('email', null, ['class' => 'form-control ' . ($errors->has('email') ? 'is-invalid': '')]) !!}
        {!! Form::hidden('organization_id', isset($organisation) ? $organisation->id : null, ['class' => 'form-control']) !!}
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-md-6">
        {!! Form::label('domain_name', 'Domain name') !!}
        {!! Form::text('domain_name', null, ['class' => 'form-control']) !!}
        {!! Form::label('domain_msg', '(Fill in, if institution has a unique website address)') !!}
    </div>
</div>

<div class="row mb-2">
    <div class="{{isset($organisation) ? 'col-md-4' : 'col-md-4'}}">
        {!! Form::label('name', 'Institution name *') !!}
        {!! Form::text('name', null, ['class' => 'form-control ' . ($errors->has('name') ? 'is-invalid': ''), 'required']) !!}
        @error('name')
        <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
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
    <div class="col-md-2">
        {!! Form::label('organisation_type', 'Type *') !!}
        {!! Form::select('organisation_type', $organization_types, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-2">
        {!! Form::label('level', 'Level') !!}
        {!! Form::select('level', $organization_levels, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>
<div class="row mb-2">
    @if(!isset($organisation))
        <div class="col-md-2">
            {!! Form::label('contact_first_name', 'Contact First name') !!}
            {!! Form::text('contact_first_name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-md-2">
            {!! Form::label('contact_last_name', 'Contact Last name') !!}
            {!! Form::text('contact_last_name', null, ['class' => 'form-control']) !!}
        </div>
    @endif
    <div class="{!! !isset($organisation) ? "col-md-4" : "col-md-6" !!}">
        {!! Form::label('address', 'Address') !!}
        {!! Form::text('address', null, ['class' => 'form-control']) !!}
    </div>
    <div class="{!! !isset($organisation) ? "col-md-2" : "col-md-3" !!}">
        {!! Form::label('zipcode', 'Zipcode') !!}
        {!! Form::text('zipcode', null, ['class' => 'form-control']) !!}
    </div>
    <div class="{!! !isset($organisation) ? "col-md-2" : "col-md-3" !!}">
        {!! Form::label('info_status', 'Info status') !!}
        {!! Form::select('info_status', $infoStatuses, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>


<div class="row mb-2">
    <div class="col-md-3">
        {!! Form::label('country', 'Country') !!}
        {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-3" id="city">
        {!! Form::label('city', 'City') !!}
        {!! Form::text('city', null, ['class' => 'form-control']) !!}
        <span class="invalid-feedback" role="alert">
            <strong>Remember to update later the city information</strong>
        </span>
    </div>
    <div class="col-md-3">
        {!! Form::label('phone', 'Phone') !!}
        {!! Form::text('phone', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('vat', 'Vat number') !!}
        {!! Form::text('vat_number', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('website', 'Website') !!}
        {!! Form::text('website', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('facebook_page', 'Facebook page') !!}
        {!! Form::text('facebook_page', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('short_description', 'Short description') !!}
        {!! Form::text('short_description', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('public_zoos_relation', 'Public zoos relation') !!}
        {!! Form::text('public_zoos_relation', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('animal_related_association', 'Animal related association') !!}
        {!! Form::text('animal_related_association', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('remarks', 'Remarks') !!}
        {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('open_remarks', 'Open remarks') !!}
        {!! Form::text('open_remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('internal_remarks', 'Internal remarks') !!}
        {!! Form::text('internal_remarks', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('mailing_category', 'Mailing category') !!}
        {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('interest_sections', 'Interest sections') !!}<br>
        @foreach($interest_sections as $section)
            <label class="checkbox-inline ml-2">
                {!! Form::checkbox('interest_section[]', $section->key, (isset($organizationInterestSections) && $organizationInterestSections->contains($section->key)) ? true : false) !!} {{$section->label}}
            </label>
        @endforeach
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-12">
        {!! Form::label('associations', 'Associations') !!}<br>
        @foreach($associations as $association)
            <label class="checkbox-inline ml-2">
                {!! Form::checkbox('associations[]', $association->key, (isset($organizationAssociations) && $organizationAssociations->contains($association->key)) ? true : false) !!} {{$association->label}}
            </label>
        @endforeach
    </div>
</div>

<hr/>


<button class="btn btn-primary btn-lg saveInstitution" type="submit" id="submitBtnInstitution">{{ $submitButtonText }}</button>
@if (isset($organisation))
    <a href="{{ route('organisations.show', $organisation) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('organisations.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif
