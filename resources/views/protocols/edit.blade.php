@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit protocol</h1>

            {!! Form::model($protocol, ['method' => 'PATCH', 'route' => ['protocols.update', $protocol->id], 'files' => 'true' ] ) !!}

            @include('protocols._form', ['submitButtonText' => 'Edit protocol'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
