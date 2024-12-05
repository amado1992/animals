@include('components.errorlist')

<div class="mb-2">
    {!! Form::label('name', 'Name *') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
</div>

<div class="row mb-2">
	<div class="col-md-6">
		{!! Form::label('iata_code', 'IATA') !!}
		{!! Form::text('iata_code', null, ['class' => 'form-control', 'maxlength' => 3]) !!}
	</div>
	<div class="col-md-6">
		{!! Form::label('icao_code', 'ICAO *') !!}
		{!! Form::text('icao_code', null, ['class' => 'form-control', 'maxlength' => 4, 'required']) !!}
	</div>
</div>

<div class="row mb-2">
	<div class="col-md-6">
		{!! Form::label('city', 'City *') !!}
        {!! Form::text('city', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
	</div>
	<div class="col-md-6">
        {!! Form::label('country_id', 'Country *') !!}
        {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => '- select -', 'required']) !!}
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		{!! Form::label('lat', 'Latitude') !!}
		{!! Form::text('lat', null, ['class' => 'form-control', 'maxlength' => 12]) !!}
	</div>
	<div class="col-md-6">
		{!! Form::label('long', 'Longitude') !!}
		{!! Form::text('long', null, ['class' => 'form-control', 'maxlength' => 12]) !!}
	</div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($airport))
    <a href="{{ route('airports.show', $airport) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('airports.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif
