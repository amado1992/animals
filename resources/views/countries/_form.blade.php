@include('components.errorlist')

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('name', 'Name *') !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('country_code', 'Country code *') !!}
        {!! Form::text('country_code', null, ['class' => 'form-control', 'maxlength' => 5, 'required']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label('phone_code', 'Phone code') !!}
        {!! Form::text('phone_code', null, ['class' => 'form-control', 'maxlength' => 5]) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('region_id', 'Region *') !!}
        {!! Form::select('region_id', $regions, null, ['class' => 'form-control', 'placeholder' => '- select -', 'required']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('language', 'Language *') !!}
        {!! Form::select('language', ['EN' => 'English', 'ES' => 'Spanish'], null, ['class' => 'form-control', 'placeholder' => '- select -', 'required']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>

@if (isset($country))
    <a href="{{ route('countries.show', $country) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('countries.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

