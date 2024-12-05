@include('components.errorlist')

<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('name', 'Role *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-8">
        {!! Form::label('display_name', 'Display name *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('display_name', null, ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        {!! Form::label('description', 'Description *', ['class' => 'font-weight-bold']) !!}
        {!! Form::text('description', null, ['class' => 'form-control', 'maxlength' => 100, 'required']) !!}
    </div>
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('roles.index') }}" class="btn btn-link" type="button">Cancel</a>

