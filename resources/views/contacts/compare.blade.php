@extends('layouts.admin')

@section('main-content')

<div class="row justify-content-center">

  <div class="col-md-10">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Compare two contacts</h1>
            <div class="row mb-3">
                <div class="col-md-2"></div>
                <div class="col-md-8 text-center">MERGE TO</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-2"></div>
                <div class="col-md-4 text-center">{{ $contact->fullname }}</div>
                <div class="col-md-4 text-center">{{ $contactToMerge->fullname }}</div>
            </div>
            {!! Form::model($contact, ['method' => 'PATCH', 'action' => 'ContactController@merge']) !!}
                <div class="form-row">
                    {!! Form::label('title', 'Contact title *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_title',null) !!}</span>
                            </div>
                            {!! Form::text('title', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            {!! Form::hidden('contact_id', $contact->id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('title', $contactToMerge->title, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                            {!! Form::hidden('contactToMerge_id', $contactToMerge->id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    {!! Form::label('first_name', 'Contact firstname *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_first_name',null) !!}</span>
                            </div>
                            {!! Form::text('first_name', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('first_name', $contactToMerge->first_name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    {!! Form::label('last_name', 'Contact lastname *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_last_name',null) !!}</span>
                            </div>
                            {!! Form::text('last_name', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('last_name', $contactToMerge->last_name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('email', 'Contact email *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_email',null) !!}</span>
                            </div>
                            {!! Form::text('email', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('email', $contactToMerge->email, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('position', 'Position *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_position',null) !!}</span>
                            </div>
                            {!! Form::text('position', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('position', $contactToMerge->position, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('mailing_category', 'Mailing category *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_mailing_category',null) !!}</span>
                            </div>
                            {!! Form::text('mailing_category', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('mailing_category', $contactToMerge->mailing_category, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-row mt-2">
                    {!! Form::label('mobile_phone', 'Mobile phone *', ['class' => 'col-md-2']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{!! Form::checkbox('check_mobile_phone',null) !!}</span>
                            </div>
                            {!! Form::text('mobile_phone', null, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('mobile_phone', $contactToMerge->mobile_phone, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                        </div>
                    </div>
                </div>

            <hr class="mb-4">

            <button class="btn btn-primary btn-lg" type="submit">Merge</button>

            @if ($from == "contact_doubles")
                <a href="{{ route('contacts.doublesView') }}" class="btn btn-link" type="button">Cancel</a>
            @else
                <a href="{{ route('contacts.index') }}" class="btn btn-link" type="button">Cancel</a>
            @endif

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>

@endsection

@section('page-scripts')

<script type="text/javascript">

$(document).ready(function() {
    $(':checkbox:checked').prop('checked', false);
});

</script>

@endsection
