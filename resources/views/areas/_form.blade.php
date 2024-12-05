@include('components.errorlist')

<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('name', 'Name *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('short_cut', 'Short name *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('short_cut', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
@if (isset($area))
    <a href="{{ route('areas.show', $area) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('areas.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif

