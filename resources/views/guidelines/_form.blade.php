@include('components.errorlist')

<div class="mb-3">
    {!! Form::label('subject', 'Subject *') !!}
    {!! Form::text('subject', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('remark', 'Remark *') !!}
    {!! Form::text('remark', null, ['class' => 'form-control', 'required']) !!}
</div>

<div class="mb-3">
    {!! Form::label('category', 'Category *') !!}
    {!! Form::select('category', ['general' => 'General', 'requests' => 'Requests', 'orders' => 'Orders', 'contacts' => 'Contacts', 'mailing' => 'Mailing'], null, ['class' => 'form-control', 'required', 'placeholder' => '- select -']) !!}
</div>

<div class="mb-3">
    {!! Form::label('related_file', 'Related file') !!}
    {!! Form::file('relatedFile') !!}
</div>

<hr class="mb-4">

<button class="btn btn-primary btn-lg" type="submit">{{ $submitButtonText }}</button>
<a href="{{ route('guidelines.index') }}" class="btn btn-link" type="button">Cancel</a>
