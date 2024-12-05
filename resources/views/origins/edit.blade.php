@extends('layouts.admin')

@section('main-content')

<div class="row">

  <div class="col-md-6 offset-md-3">

    <div class="card shadow mb-4">

        <div class="card-body">

          <h1 class="mb-4">Edit origin {{ $origin->name }}</h1>

          {!! Form::model($origin, ['method' => 'PATCH', 'route' => ['origins.update', $origin->id] ] ) !!}

              @include('origins._form', ['submitButtonText' => 'Edit origin'])

          {!! Form::close() !!}

        </div>
      </div>

    </div>
  </div>


@endsection

