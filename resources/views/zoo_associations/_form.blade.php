@include('components.errorlist')

<div class="mb-3">
    {!! Form::label('area', 'Area *') !!}
    {!! Form::text('area', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('name', 'Name *') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('website', 'Website *') !!}
    {!! Form::text('website', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('status', 'Status *') !!}
    {!! Form::select('status', ['' => '', 'Interesting' => 'Interesting', 'Very interesting' => 'Very interesting', 'Not interesting' => 'Not interesting', 'Done' => 'Done', 'Error' => 'Error'], null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('started_on', 'Started on') !!}
    {!! Form::date('started_date', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('remark', 'Remark') !!}
    {!! Form::text('remark', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('checked_on', 'Checked on') !!}
    {!! Form::date('checked_date', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('checked_by', 'Checked by') !!}
    {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('zoo-associations.index') }}" class="btn btn-link" type="button">Cancel</a>
