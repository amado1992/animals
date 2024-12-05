@include('components.errorlist')

<div class="mb-2">
    {!! Form::label('subject', 'Subject *') !!}
    {!! Form::text('subject', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('date_created', 'Date created') !!}
        {!! Form::date('date_created', null, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('date_sent_out', 'Date sent out') !!}
        {!! Form::date('date_sent_out', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-4">
        {!! Form::label('language', 'Language') !!}
        {!! Form::text('language', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('institution_level', 'Level') !!}
        {!! Form::text('institution_level', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-4">
        {!! Form::label('institution_types', 'Institution types') !!}
        {!! Form::text('institution_types', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="mb-2">
    {!! Form::label('part_of_world', 'Part of world') !!}
    {!! Form::text('part_of_world', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('exclude_continents', 'Exclude continents') !!}
    {!! Form::text('exclude_continents', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('exclude_countries', 'Exclude countries') !!}
    {!! Form::text('exclude_countries', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-3">
    {!! Form::label('remarks', 'Remarks') !!}
    {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('related_file', 'Mailing template') !!}
    {!! Form::file('relatedFile') !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('mailings.index') }}" class="btn btn-link" type="button">Cancel</a>
