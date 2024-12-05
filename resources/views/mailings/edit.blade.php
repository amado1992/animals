@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit mailing</h1>

            {!! Form::model($mailing, ['method' => 'PATCH', 'route' => ['mailings.update', $mailing->id], 'files' => 'true' ] ) !!}

            @include('mailings._form', ['submitButtonText' => 'Edit mailing'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
