@include('components.errorlist')

<div class="row mb-2">
    <div class="col-md-6">
        {!! Form::label('next_reminder_at', 'Reminder date') !!}
        {!! Form::date('next_reminder_at', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="mb-2">
    {!! Form::label('remarks', 'Remarks') !!}
    {!! Form::text('remarks', null, ['class' => 'form-control']) !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('search-mailings.index') }}" class="btn btn-link" type="button">Cancel</a>
