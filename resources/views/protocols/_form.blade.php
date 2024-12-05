@include('components.errorlist')

<div class="row mb-3">
    <div class="col-md-4">
        {!! Form::label('section', 'Section *') !!}
        {!! Form::select('section', $categories, null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
    </div>
</div>

<div class="mb-3">
    {!! Form::label('subject', 'Subject *') !!}
    {!! Form::text('subject', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('remark', 'Remark *') !!}
    {!! Form::text('remark', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('related_file', 'Related file') !!}
    {!! Form::file('relatedFile') !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('protocols.index', session('protocol_section')) }}" class="btn btn-link" type="button">Cancel</a>
