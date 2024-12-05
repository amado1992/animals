@include('components.errorlist')

<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('title', 'Title *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('title', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('name', 'Name *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('icon', 'Icon *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('icon', null, ['class' => 'form-control', 'maxlength' => 50]) !!}
    </div>
</div>
<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('directories.index') }}" class="btn btn-link" type="button">Cancel</a>

