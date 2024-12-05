@include('components.errorlist')

<div class="mb-3">
    {!! Form::label('contact_email', 'Email address *') !!}
    {!! Form::text('contact_email', null, ['class' => 'form-control']) !!}
</div>

<div class="row mb-2">
    <div class="col-md-2">
        {!! Form::label('title', 'Title') !!}
        {!! Form::select('title', ['Mr.' => 'Mr.', 'Mrs.' => 'Mrs.', 'Ms.' => 'Ms.', 'Dr.' => 'Dr.', 'Ing.' => 'Ing.'], null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::label('first_name', 'First name *') !!}
        {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col-md-5">
        {!! Form::label('last_name', 'Last name *') !!}
        {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="mb-2">
    {!! Form::label('mobile_phone', 'Phone number') !!}
    {!! Form::text('mobile_phone', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('position', 'Position') !!}
    {!! Form::text('position', null, ['class' => 'form-control']) !!}
</div>

<div class="mb-2">
    {!! Form::label('member_approved_status', 'Approved status') !!}
    {!! Form::select('member_approved_status', $member_approved_status, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>

<div class="mb-2">
    {!! Form::label('mailing_category', 'Mailing category') !!}
    {!! Form::select('mailing_category', $mailing_categories, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>

<hr class="mb-4">

<h4>Contact works for</h4>

<div class="mb-3">
    {!! Form::label('organisation_id', 'Institution') !!}
    {!! Form::select('organisation_id', $organisations, null, ['class' => 'form-control', 'placeholder' => '- select -']) !!}
</div>

or <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">create new organisation</a>

<div class="collapse" id="collapseExample" class="mt-4">
    @include('organisations._form')
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitContactButtonText }}</button>
@if (isset($contact))
    <a href="{{ route('contacts-deleted.show', $contact) }}" class="btn btn-link" type="button">Cancel</a>
@else
    <a href="{{ route('contacts-deleted.index') }}" class="btn btn-link" type="button">Cancel</a>
@endif
