@include('components.errorlist')

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            {!! Form::label('email', 'E-mail address *') !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'maxlength' => 100, 'required']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            {!! Form::label('role', 'Role *') !!}
            {!! Form::select('role', $roles, (isset($user)) ? $user->roles()->first()->id : null, ['class' => 'form-control', 'placeholder' => '- select -', 'required']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            {!! Form::label('name', 'First name *') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'maxlength' => 100, 'required']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {!! Form::label('last_name', 'Last name *') !!}
            {!! Form::text('last_name', null, ['class' => 'form-control', 'maxlength' => 100, 'required']) !!}
        </div>
    </div>
</div>

<div class="mb-3">
    {!! Form::label('password', 'Password *') !!}
    {!! Form::password('password', ['class' => 'form-control']) !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('users.index') }}" class="btn btn-link" type="button">Cancel</a>
