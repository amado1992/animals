@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

            <h1 class="mb-4">Edit code</h1>

            {!! Form::model($code, ['method' => 'PATCH', 'route' => ['codes.update', $code->id] ] ) !!}

            @include('codes._form', ['submitButtonText' => 'Edit code'])

            {!! Form::close() !!}
        </div>
      </div>

    </div>
  </div>

@endsection
